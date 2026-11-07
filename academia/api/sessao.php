<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';

responderJson(200, [
    'autenticado' => isset($_SESSION['star_usuario_id']),
    'nome' => $_SESSION['star_usuario_nome'] ?? null,
    'csrf' => $_SESSION['star_csrf'],
]);
