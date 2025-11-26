<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Story.php';
require_once __DIR__ . '/../Models/Adoption.php';

/**
 * Controlează fluxul "Povești fericite": listează adopțiile aprobate și
 * permite utilizatorilor autentificați să scrie povești și să atașeze imagini.
 */
class StoryController extends BaseController {

  private function ensureLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user'])) {
      $this->redirect('index.php?controller=auth&action=loginForm');
    }
  }

  // Lista poveștilor fericite
  public function index(): void {
    $storyModel = new Story();
    // Returnează toate animalele adoptate (aprobate) împreună cu povestea dacă există
    $stories = $storyModel->getAllAdoptedWithOptionalStories();

    $this->render('stories/index', [
      'title' => 'Povești fericite',
      'stories' => $stories
    ]);
  }

  // Formular pentru a crea o poveste pentru un animal adoptat
  public function create(): void {
    $this->ensureLoggedIn();

    $animalId = $_GET['animal_id'] ?? null;
    if (!$animalId) {
      http_response_code(400);
      exit('ID animal lipsă');
    }

    // Verificăm că utilizatorul este adoptator aprobat
    $adoptionModel = new Adoption();
    if (!$adoptionModel->isUserAdopter((int)$_SESSION['user']['id'], (int)$animalId)) {
      http_response_code(403);
      exit('Doar adoptatorul poate adăuga o poveste pentru acest animal.');
    }

    $this->render('stories/create', [
      'title' => 'Adaugă o poveste fericită',
      'animal_id' => (int)$animalId
    ]);
  }

  // Salvează povestea
  public function store(): void {
    $this->ensureLoggedIn();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('index.php?controller=story&action=index');
      return;
    }

    $this->verifyCsrf();

    $animalId = (int)($_POST['animal_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');

    if (!$animalId || $content === '') {
      http_response_code(400);
      exit('Date incomplete');
    }

    // Verificăm din nou dreptul de a scrie povestea
    $adoptionModel = new Adoption();
    if (!$adoptionModel->isUserAdopter((int)$_SESSION['user']['id'], $animalId)) {
      http_response_code(403);
      exit('Doar adoptatorul poate adăuga o poveste pentru acest animal.');
    }

    $imageNames = [];
    // support multiple file uploads from input name="images[]"
    if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
      $targetDir = __DIR__ . '/../../public/uploads/';
      if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
      for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
        if (empty($_FILES['images']['name'][$i])) continue;
        $base = basename($_FILES['images']['name'][$i]);
        $fileName = time() . '_' . $i . '_' . $base;
        if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $targetDir . $fileName)) {
          $imageNames[] = $fileName;
        }
      }
    } elseif (!empty($_FILES['image']['name'])) {
      // backwards-compat single image field fallback
      $targetDir = __DIR__ . '/../../public/uploads/';
      if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
      $fileName = time() . '_' . basename($_FILES['image']['name']);
      if (move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $fileName)) {
        $imageNames[] = $fileName;
      }
    }

    // Store as JSON array of filenames (empty array -> null)
    $imageJson = !empty($imageNames) ? json_encode($imageNames) : null;

    $storyModel = new Story();
    // Title removed from UI; store empty title to remain compatible with schema
    $storyModel->create($animalId, '', $content, $imageJson);

    $this->redirect('index.php?controller=story&action=index');
  }

  // Afișează o singură poveste (pentru animal_id)
  public function show(): void {
    $animalId = (int)($_GET['animal_id'] ?? 0);
    if (!$animalId) {
      http_response_code(400);
      exit('ID animal lipsă');
    }

    $storyModel = new Story();
    $story = $storyModel->getByAnimalId($animalId);
    if (empty($story)) {
      http_response_code(404);
      exit('Povestea nu a fost găsită.');
    }

    $this->render('stories/show', [
      'title' => 'Povestea mea',
      'story' => $story
    ]);
  }

  // Formular de editare pentru o poveste existentă
  public function edit(): void {
    $this->ensureLoggedIn();
    $animalId = (int)($_GET['animal_id'] ?? 0);
    if (!$animalId) {
      http_response_code(400);
      exit('ID animal lipsă');
    }

    $adoptionModel = new Adoption();
    if (!$adoptionModel->isUserAdopter((int)$_SESSION['user']['id'], $animalId)) {
      http_response_code(403);
      exit('Doar adoptatorul poate edita povestea pentru acest animal.');
    }

    $storyModel = new Story();
    $story = $storyModel->getByAnimalId($animalId);
    if (empty($story)) {
      http_response_code(404);
      exit('Povestea nu există.');
    }

    $this->render('stories/edit', [
      'title' => 'Editează povestea',
      'story' => $story
    ]);
  }

  // Actualizează o poveste existentă
  public function update(): void {
    $this->ensureLoggedIn();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('index.php?controller=story&action=index');
      return;
    }

    $this->verifyCsrf();

    $animalId = (int)($_POST['animal_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');

    if (!$animalId || $content === '') {
      http_response_code(400);
      exit('Date incomplete');
    }

    $adoptionModel = new Adoption();
    if (!$adoptionModel->isUserAdopter((int)$_SESSION['user']['id'], $animalId)) {
      http_response_code(403);
      exit('Doar adoptatorul poate edita povestea pentru acest animal.');
    }

    $storyModel = new Story();
    $existing = $storyModel->getByAnimalId($animalId);

    $imageNames = [];
    $targetDir = __DIR__ . '/../../public/uploads/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
      // If user uploaded new files, replace existing images
      for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
        if (empty($_FILES['images']['name'][$i])) continue;
        $base = basename($_FILES['images']['name'][$i]);
        $fileName = time() . '_' . $i . '_' . $base;
        if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $targetDir . $fileName)) {
          $imageNames[] = $fileName;
        }
      }
    } elseif (!empty($_FILES['image']['name'])) {
      $fileName = time() . '_' . basename($_FILES['image']['name']);
      if (move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $fileName)) {
        $imageNames[] = $fileName;
      }
    }

    // Determine retained existing images (checkboxes from the edit form)
    $kept = $_POST['existing_images'] ?? [];
    if (!is_array($kept)) $kept = [$kept];
    // Normalize kept values to strings
    $kept = array_values(array_map('strval', $kept));

    // Final images = kept existing images + newly uploaded images (append)
    $final = $kept;
    if (!empty($imageNames)) {
      $final = array_merge($final, $imageNames);
    }

    $imageJson = !empty($final) ? json_encode($final) : null;

    // Update story (title kept empty for compatibility)
    $storyModel->updateStory($animalId, '', $content, $imageJson);

    $this->redirect('index.php?controller=story&action=show&animal_id=' . $animalId);
  }
}
