<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Animal.php';
require_once __DIR__ . '/../Models/Match.php';
// matching config
// load with $cfg = require __DIR__ . '/../../config/matching.php';

/**
 * Gestionează chestionarul de potrivire: colectează răspunsurile utilizatorului,
 * calculează scorul pentru fiecare animal și salvează rezultatele relevante.
 */
class MatchController extends BaseController {

  // ✅ Afișează formularul
  public function form(): void {
    $this->render('match/form', ['title' => 'Potrivire inteligentă']);
  }

public function calculate(): void {
  if (session_status() === PHP_SESSION_NONE) session_start();
  if (empty($_SESSION['user'])) {
    $this->redirect('index.php?controller=auth&action=loginForm');
    return;
  }

  $lifestyle = $_POST['lifestyle'] ?? 'medium';
  $space = $_POST['space'] ?? 'medium';
  $time = $_POST['time'] ?? 'medium';
  $experience = $_POST['experience'] ?? 'some';
  $temperament = $_POST['temperament'] ?? 'playful';

  $animalModel = new Animal();
  $animals = $animalModel->getAllAvailable();
  $results = [];

  // load matching config (weights, keywords)
  $matchCfg = [];
  $cfgPath = __DIR__ . '/../../config/matching.php';
  if (is_file($cfgPath)) {
    $matchCfg = require $cfgPath;
  }
  $weights = $matchCfg['weights'] ?? [];
  $keywords = $matchCfg['keywords'] ?? [];

  // helper closures
  $age_similarity = function(int $animalMonths, int $idealMonths = 36): float {
    $diffYears = abs($animalMonths - $idealMonths) / 12.0;
    // sigma = 1 year
    return exp(-($diffYears * $diffYears) / 2.0);
  };

  $has_keyword = function(string $haystack, array $candidates) {
    foreach ($candidates as $kw) {
      if ($kw === '') continue;
      if (stripos($haystack, $kw) !== false) return true;
    }
    return false;
  };

  foreach ($animals as $animal) {
    $rawScore = 0.0;
    $contrib = [];

    $desc = (string)($animal['description'] ?? '');

    // lifestyle
    if ($lifestyle === 'active') {
      if (($animal['species'] ?? '') === 'Câine') { $val = ($weights['species_dog_for_active'] ?? 30); $rawScore += $val; $contrib['species_dog_for_active'] = $val; }
      if ($has_keyword($desc, $keywords['energetic'] ?? ['energic'])) { $val = ($weights['description_energetic'] ?? 20); $rawScore += $val; $contrib['description_energetic'] = $val; }
    } elseif ($lifestyle === 'relaxed') {
      if (in_array(($animal['species'] ?? ''), ['Pisică', 'Iepure'], true)) { $val = 25; $rawScore += $val; $contrib['species_relaxed'] = $val; }
      if (stripos($desc, 'calm') !== false) { $val = 15; $rawScore += $val; $contrib['description_calm'] = $val; }
    }

    // space
  if ($space === 'large' && ($animal['species'] ?? '') === 'Câine') { $val = ($weights['space_large_dog'] ?? 15); $rawScore += $val; $contrib['space_large_dog'] = $val; }
  if ($space === 'small' && in_array(($animal['species'] ?? ''), ['Pisică', 'Iepure', 'Hamster'], true)) { $val = ($weights['space_small_pet'] ?? 15); $rawScore += $val; $contrib['space_small_pet'] = $val; }

    // time / temperament
  if ($time === 'high' && $has_keyword($desc, $keywords['playful'] ?? ['jucăuș'])) { $val = ($weights['time_playful'] ?? 15); $rawScore += $val; $contrib['time_playful'] = $val; }
  if ($time === 'low' && $has_keyword($desc, $keywords['independent'] ?? ['independent'])) { $val = ($weights['time_independent'] ?? 15); $rawScore += $val; $contrib['time_independent'] = $val; }

    // experience -> prefer age similarity
    $ageMonths = (int)($animal['age_months'] ?? 0);
    if ($experience === 'none') {
      $val = ($weights['experience_young'] ?? 10) * $age_similarity($ageMonths, 6);
      $rawScore += $val; $contrib['experience_young'] = $val;
    } elseif ($experience === 'a_lot') {
      $val = ($weights['experience_old'] ?? 10) * $age_similarity($ageMonths, 36);
      $rawScore += $val; $contrib['experience_old'] = $val;
    }

    // temperament
  if ($temperament === 'playful' && $has_keyword($desc, $keywords['playful'] ?? ['jucăuș'])) { $val = ($weights['temperament_match'] ?? 10); $rawScore += $val; $contrib['temperament_playful'] = $val; }
  if ($temperament === 'calm' && stripos($desc, 'calm') !== false) { $val = ($weights['temperament_match'] ?? 10); $rawScore += $val; $contrib['temperament_calm'] = $val; }

    // friendly
  if ($has_keyword($desc, $keywords['friendly'] ?? ['prieteno','afectuos'])) { $val = ($weights['friendly_keyword'] ?? 5); $rawScore += $val; $contrib['friendly_keyword'] = $val; }

    // normalize to 0..100
    $maxPossible = array_sum($weights) ?: 1;
    $compatibility = (int)round(min(100, ($rawScore / $maxPossible) * 100));

    $results[] = [ 'animal' => $animal, 'compatibility' => $compatibility, 'rawScore' => $rawScore, 'details' => $contrib ];
  }

  // If debug flag is provided, output JSON with breakdown for inspection
  if (!empty($_GET['debug'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
  }

  usort($results, fn($a, $b) => $b['compatibility'] <=> $a['compatibility']);

  // ✅ Salvează rezultatele în tabelul matches
  $matchModel = new MatchModel();
  $userId = (int)$_SESSION['user']['id'];
  
  // Salvează doar potrivirile cu compatibilitate >= 50% pentru a evita spam-ul în baza de date
  foreach ($results as $result) {
    if ($result['compatibility'] >= 50) {
      try {
        $matchModel->create($userId, (int)$result['animal']['id'], $result['compatibility']);
      } catch (Exception $e) {
        // Ignoră erorile de duplicate sau alte probleme minore
        // (poți adăuga logging aici dacă e necesar)
      }
    }
  }

  $this->render('match/result', [
    'title' => 'Rezultatul potrivirii',
    'results' => $results
  ]);
}

}
