<?php
declare(strict_types=1);

/**
 * Banco existente do CMS; tabela separada para as contas dos clientes do site.
 * No XAMPP padrão o usuário root não tem senha. Ajuste se for diferente.
 */
function conectarBanco(): PDO
{
    $host  = getenv('STAR_DB_HOST') ?: '127.0.0.1';
    $porta = getenv('STAR_DB_PORT') ?: '3306';
    $banco = getenv('STAR_DB_NAME') ?: 'cms_api';
    $user  = getenv('STAR_DB_USER') ?: 'root';
    $senha = getenv('STAR_DB_PASS');
    if ($senha === false) {
        $senha = '';
    }

    $dsn = "mysql:host={$host};port={$porta};dbname={$banco};charset=utf8mb4";
    return new PDO($dsn, $user, $senha, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
