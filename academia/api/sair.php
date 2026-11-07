<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método não permitido.');
}
if (!validarCsrf($_POST['csrf'] ?? null)) {
    http_response_code(403);
    exit('A sessão expirou. Volte à página anterior e atualize.');
}

// Encerra somente a sessão do site, sem afetar outras aplicações no XAMPP.
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'],
    ]);
}
session_destroy();
header('Location: ../login.html', true, 303);
exit;
