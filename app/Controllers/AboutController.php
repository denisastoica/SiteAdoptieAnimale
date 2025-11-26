<?php
require_once __DIR__ . '/BaseController.php';

/**
 * Controllerul paginii "Despre noi" – livrează conținut static și informații
 * despre misiunea platformei fără logică suplimentară.
 */
class AboutController extends BaseController { //extend inseamna mostenire in php

  public function index(): void {
    $this->render('about', [ //&this se refera la clasa curenta
      'title' => 'Despre noi'
    ]);
  }

}
//about controller este controllerul care se ocupa cu pagina despre noi a site-ului, 
//mosteneste metoda render din BaseController pentru a afisa view-ul corespunzator.