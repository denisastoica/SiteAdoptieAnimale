<?php
require_once __DIR__ . '/BaseController.php';

/**
 * Livrează pagina principală (landing) cu slideshow-urile și link-urile rapide.
 */
class HomeController extends BaseController {
  public function home() {
    $this->render('home', ['title' => 'Acasă']);
  }
}
