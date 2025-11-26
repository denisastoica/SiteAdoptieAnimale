<?php
require_once __DIR__ . '/../config/config.php';
session_start();

// 🔹 Controller și acțiune din URL (ex: ?controller=auth&action=login)
$controllerName = $_GET['controller'] ?? 'home';
$actionName = $_GET['action'] ?? 'home';

// 🔹 Calea către fișierul controllerului
$controllerFile = "../app/Controllers/" . ucfirst($controllerName) . "Controller.php";

// 🔹 Verificăm existența controllerului
if (!file_exists($controllerFile)) {
    http_response_code(404);
    exit("Controllerul '$controllerName' nu există.");
}

require_once $controllerFile;

// 🔹 Numele clasei controllerului
$controllerClass = ucfirst($controllerName) . "Controller";

// 🔹 Verificăm dacă clasa există
if (!class_exists($controllerClass)) {
    http_response_code(500);
    exit("Clasa $controllerClass nu există în fișierul controllerului.");
}

// 🔹 Instanțiem controllerul
$controller = new $controllerClass();

// 🔹 Verificăm dacă acțiunea există
if (!method_exists($controller, $actionName)) {
    http_response_code(404);
    exit("Acțiunea '$actionName' nu există în controllerul '$controllerClass'.");
}

// ✅ Executăm acțiunea, cu protecție la erori CSRF
try {
    $controller->$actionName();
} catch (CsrfException $e) {
    // 🛡️ Eroare CSRF (token invalid)
    http_response_code(400);
    echo "<!DOCTYPE html>
    <html lang='ro'>
    <head><meta charset='UTF-8'><title>Eroare CSRF</title></head>
    <body style='font-family: Arial; text-align:center; margin-top:50px;'>
      <h2 style='color:red;'>Eroare de securitate:</h2>
      <p>" . htmlspecialchars($e->getMessage()) . "</p>
      <a href='/site/public/index.php?controller=auth&action=loginForm' 
         style='color:blue; text-decoration:underline;'>Înapoi la autentificare</a>
    </body></html>";
    exit;
} catch (Throwable $e) {
    // ⚠️ Alte erori neprevăzute (de ex. probleme SQL sau controller)
    http_response_code(500);
    echo "<!DOCTYPE html>
    <html lang='ro'>
    <head><meta charset='UTF-8'><title>Eroare aplicație</title></head>
    <body style='font-family: Arial; text-align:center; margin-top:50px;'>
      <h2 style='color:red;'>Eroare internă:</h2>
      <p>" . htmlspecialchars($e->getMessage()) . "</p>
      <a href='/site/public/index.php' 
         style='color:blue; text-decoration:underline;'>Înapoi la prima pagină</a>
    </body></html>";
    exit;
}
