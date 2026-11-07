<?php
declare(strict_types=1);

// Sessão exclusiva para clientes da Star Fitness, independente do painel CMS.
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_name('STARFITSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (empty($_SESSION['star_csrf'])) {
    $_SESSION['star_csrf'] = bin2hex(random_bytes(32));
}

function validarCsrf(?string $token): bool
{
    return is_string($token) && hash_equals($_SESSION['star_csrf'], $token);
}

function responderJson(int $status, array $dados): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}
