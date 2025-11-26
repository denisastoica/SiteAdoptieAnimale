  <?php
  require_once __DIR__ . '/BaseController.php';
  require_once __DIR__ . '/../Models/Animal.php';
  require_once __DIR__ . '/../Models/Species.php';
  require_once __DIR__ . '/../Models/Location.php';
  require_once __DIR__ . '/../Models/Adoption.php';
  require_once __DIR__ . '/../Models/Story.php';

  class AnimalController extends BaseController {

    private function ensureAdmin() {
      if (session_status() === PHP_SESSION_NONE) session_start();
      if (empty($_SESSION['user']) || ($_SESSION['user']['role_id'] ?? 1) != 2) {
        http_response_code(403);
        exit('Acces interzis – doar pentru administratori.');
      }
    }

    private function ensureLoggedIn() {
      if (session_status() === PHP_SESSION_NONE) session_start();
      if (empty($_SESSION['user'])) {
        $this->redirect('index.php?controller=auth&action=loginForm');
      }
    }

    public function index(): void {
      $animal = new Animal();
      $animals = $animal->allForListing();

      $this->render('animals/list', [
        'animals' => $animals,
        'title' => 'Animale disponibile pentru adopție'
      ]);
    }

    public function create(): void {
      $this->ensureLoggedIn();

      $speciesModel = new Species();
      $locationModel = new Location();

      $species = $speciesModel->all();
      $locations = $locationModel->all();

      $this->render('animals/create', [
        'title' => 'Adaugă un animal',
        'species' => $species,
        'locations' => $locations
      ]);
    }

    public function store(): void {
      $this->ensureLoggedIn();
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('index.php?controller=animal&action=create');
        return;
      }

      $this->verifyCsrf();

      $animal = new Animal();
      $animal->name = $_POST['name'] ?? '';
      $animal->breed = $_POST['breed'] ?? '';
      $animal->sex = $_POST['sex'] ?? '';
      $animal->age_months = (int)($_POST['age_months'] ?? 0);
      $animal->description = $_POST['description'] ?? '';
      $animal->species_id = (int)($_POST['species_id'] ?? 0);
      $animal->location_id = (int)($_POST['location_id'] ?? 0);
  $animal->status = 'available';


      if (!empty($_FILES['image']['name'])) {
        $targetDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $fileName);
        $animal->image = $fileName;
      }

      $newId = $animal->create();

      if (!empty($_SESSION['user'])) {
        $storyContent = trim($_POST['story_content'] ?? '');
        $storyImages = [];
        $targetDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        if (!empty($_FILES['story_images']) && is_array($_FILES['story_images']['name'])) {
          for ($i = 0; $i < count($_FILES['story_images']['name']); $i++) {
            if (empty($_FILES['story_images']['name'][$i])) continue;
            $base = basename($_FILES['story_images']['name'][$i]);
            $fileName = time() . '_st_' . $i . '_' . $base;
            if (move_uploaded_file($_FILES['story_images']['tmp_name'][$i], $targetDir . $fileName)) {
              $storyImages[] = $fileName;
            }
          }
        }

        if ($storyContent !== '' || !empty($storyImages)) {
          $storyModel = new Story();
          $imgJson = !empty($storyImages) ? json_encode($storyImages) : null;
          $storyModel->create($newId, '', $storyContent, $imgJson);
        }
      }

      $this->redirect('index.php?controller=animal&action=index');
    }

    public function view(): void {
      if (empty($_GET['id'])) {
        http_response_code(400);
        exit('ID animal lipsă');
      }

      $animal = (new Animal())->read((int)$_GET['id']);
      if (!$animal) {
        http_response_code(404);
        exit('Animalul nu a fost găsit');
      }

      $this->render('animals/view', [
        'animal' => $animal,
        'title' => 'Detalii animal'
      ]);
    }

    public function adopt(): void {
      if (session_status() === PHP_SESSION_NONE) session_start();
      if (empty($_SESSION['user'])) {
        $this->redirect('index.php?controller=auth&action=loginForm');
        return;
      }

      $this->verifyCsrf();

      $animalId = $_POST['id'] ?? null;
      if (!$animalId) {
        http_response_code(400);
        exit('ID animal lipsă');
      }

      $animalModel = new Animal();
      $animal = $animalModel->read((int)$animalId);
      if (!$animal || ($animal['status'] ?? 'available') !== 'available') {
        http_response_code(400);
        exit('Animalul nu este disponibil pentru adopție.');
      }

      $adoption = new Adoption();
      $adoption->createAdoption((int)$_SESSION['user']['id'], (int)$animalId);

      $this->redirect('index.php?controller=user&action=profile');
    }

    public function delete(): void {
      $this->ensureAdmin();
      $this->verifyCsrf();

      if (empty($_POST['id'])) {
        http_response_code(400);
        exit('ID invalid');
      }

      $animal = new Animal();
      $animal->delete((int)$_POST['id']);
      $this->redirect('index.php?controller=animal&action=index');
    }

    public function edit(): void {
      $this->ensureAdmin();
      $id = $_GET['id'] ?? null;
      if (!$id) {
        http_response_code(400);
        exit('ID invalid');
      }

      $animalModel = new Animal();
      $animal = $animalModel->findById($id);

      if (!$animal) {
        http_response_code(404);
        exit('Animalul nu există.');
      }

      $speciesModel = new Species();
      $locationModel = new Location();
      $species = $speciesModel->all();
      $locations = $locationModel->all();

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->verifyCsrf();

        $data = [
          'name' => $_POST['name'],
          'breed' => $_POST['breed'],
          'sex' => $_POST['sex'],
          'age_months' => $_POST['age_months'],
          'description' => $_POST['description'],
          'species_id' => $_POST['species_id'],
          'location_id' => $_POST['location_id'],
          'status' => 'available',
          'image' => $animal['image']
        ];

        if (!empty($_FILES['image']['name'])) {
          $uploadDir = __DIR__ . '/../../public/uploads/';
          if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
          $fileName = time() . '_' . basename($_FILES['image']['name']);
          $uploadFile = $uploadDir . $fileName;
          move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);
          $data['image'] = $fileName;
        }

        $animalModel->update($id, $data);

        if (!empty($_SESSION['user']) && (($_SESSION['user']['role_id'] ?? 1) == 2)) {
          $storyContent = trim($_POST['story_content'] ?? '');
          $storyImages = [];
          $targetDir = __DIR__ . '/../../public/uploads/';
          if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
          if (!empty($_FILES['story_images']) && is_array($_FILES['story_images']['name'])) {
            for ($i = 0; $i < count($_FILES['story_images']['name']); $i++) {
              if (empty($_FILES['story_images']['name'][$i])) continue;
              $base = basename($_FILES['story_images']['name'][$i]);
              $fileName = time() . '_st_' . $i . '_' . $base;
              if (move_uploaded_file($_FILES['story_images']['tmp_name'][$i], $targetDir . $fileName)) {
                $storyImages[] = $fileName;
              }
            }
          }

          $storyModel = new Story();
          $existing = $storyModel->getByAnimalId($id);

          if ($existing) {
            $existingImgs = [];
            $dec = json_decode($existing['image'], true);
            if (is_array($dec)) $existingImgs = $dec;
            elseif (!empty($existing['image'])) $existingImgs = [$existing['image']];

            $finalImgs = array_merge($existingImgs, $storyImages);
            $imgJson = !empty($finalImgs) ? json_encode($finalImgs) : null;
            $finalContent = $storyContent !== '' ? $storyContent : $existing['content'];
            $storyModel->updateStory($id, '', $finalContent, $imgJson);
          } else {
            if ($storyContent !== '' || !empty($storyImages)) {
              $imgJson = !empty($storyImages) ? json_encode($storyImages) : null;
              $storyModel->create($id, '', $storyContent, $imgJson);
            }
          }
        }

        $this->redirect('index.php?controller=animal&action=index');
      }

      $this->render('animals/edit', [
        'animal' => $animal,
        'species' => $species,
        'locations' => $locations,
        'title' => 'Editează animal'
      ]);
    }
  }
