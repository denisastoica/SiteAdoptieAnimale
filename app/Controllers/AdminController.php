<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Adoption.php';
require_once __DIR__ . '/../Models/Animal.php';
require_once __DIR__ . '/../Models/Contact.php';

/**
 * Gestionează zona de administrare: aprobă/revocă adopții, șterge animale,
 * procesează mesajele de contact și aplică filtre de autorizare pentru admini.
 */
class AdminController extends BaseController {

  private function ensureAdmin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
      http_response_code(403);
      exit('Acces interzis – doar pentru administratori.');
    }
  }

  public function adoptions(): void {
    $this->ensureAdmin();

    $adoptionModel = new Adoption();
    $pending = $adoptionModel->getAll();

    $this->render('admin/adoptions', [
      'title' => 'Cereri de adopție în așteptare',
      'pending' => $pending
    ]);
  }

  public function approve(): void {
    $this->ensureAdmin();
    $this->verifyCsrf();

    $id = $_POST['id'] ?? null;
    if (!$id) exit('ID invalid');

    $adoption = new Adoption();
    $adoption->status = 'approved';
    $adoption->update($id, ['status' => 'approved']);

    // Setează animalul ca adoptat
    $adoptionData = $adoption->read($id);
    if ($adoptionData && !empty($adoptionData['animal_id'])) {
      $animal = new Animal();
      $animal->updateStatus((int)$adoptionData['animal_id'], 'adopted');
    }

    $this->redirect('index.php?controller=admin&action=adoptions');
  }

  public function reject(): void {
    $this->ensureAdmin();
    $this->verifyCsrf();

    $id = $_POST['id'] ?? null;
    if (!$id) exit('ID invalid');

    $adoption = new Adoption();
    $adoption->status = 'rejected';
    $adoption->update($id, ['status' => 'rejected']);

    // Dacă respingem cererea, marcăm animalul ca disponibil din nou
    $adoptionData = $adoption->read($id);
    if ($adoptionData && !empty($adoptionData['animal_id'])) {
      $animal = new Animal();
      $animal->updateStatus((int)$adoptionData['animal_id'], 'available');
    }

    $this->redirect('index.php?controller=admin&action=adoptions');
  }

  public function deleteAnimal(): void {
    $this->ensureAdmin();
    $this->verifyCsrf();

    $id = $_POST['id'] ?? null;
    if (!$id) exit('ID invalid');

    $animal = new Animal();
    $animal->delete($id);

    $this->redirect('index.php?controller=animal&action=index');
  }

  // List contact messages for admin
  public function contacts(): void {
    $this->ensureAdmin();
    $contact = new Contact();
    $rows = $contact->all();
    $this->render('admin/contacts', [
      'title' => 'Mesaje contact',
      'contacts' => $rows
    ]);
  }

  // Update status of a contact message (e.g., mark as read)
  public function contactUpdateStatus(): void {
    $this->ensureAdmin();
    $this->verifyCsrf();
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
      http_response_code(400); exit('ID invalid');
    }
    $contact = new Contact();
    // Only allow marking as read from the admin UI. Archiving was removed per request.
    $contact->updateStatus($id, 'read');
    $this->redirect('index.php?controller=admin&action=contacts');
  }

  // Delete a contact message
  public function contactDelete(): void {
    $this->ensureAdmin();
    $this->verifyCsrf();
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
      http_response_code(400); exit('ID invalid');
    }
    $contact = new Contact();
    $contact->delete($id);
    $this->redirect('index.php?controller=admin&action=contacts');
  }
}
