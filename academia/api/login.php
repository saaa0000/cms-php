<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responderJson(405, ['erro' => 'Método não permitido.']);
}
if (!validarCsrf($_POST['csrf'] ?? null)) {
    responderJson(403, ['erro' => 'Sessão expirada. Atualize a página e tente novamente.']);
}

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$senha = (string) ($_POST['senha'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
    responderJson(422, ['erro' => 'Informe e-mail e senha válidos.']);
}

// Limite básico por sessão, para desestimular tentativas automatizadas.
$agora = time();
if (!isset($_SESSION['star_tentativas_inicio']) || $agora - $_SESSION['star_tentativas_inicio'] > 600) {
    $_SESSION['star_tentativas_inicio'] = $agora;
    $_SESSION['star_tentativas'] = 0;
}
if ($_SESSION['star_tentativas'] >= 10) {
    responderJson(429, ['erro' => 'Muitas tentativas. Tente novamente em alguns minutos.']);
}

try {
    $pdo = conectarBanco();
    $stmt = $pdo->prepare('SELECT id, nome, senha_hash FROM contas_star_fitness WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
        $_SESSION['star_tentativas']++;
        responderJson(401, ['erro' => 'E-mail ou senha incorretos.']);
    }

    if (password_needs_rehash($usuario['senha_hash'], PASSWORD_DEFAULT)) {
        $alterar = $pdo->prepare('UPDATE contas_star_fitness SET senha_hash = :hash WHERE id = :id');
        $alterar->execute([':hash' => password_hash($senha, PASSWORD_DEFAULT), ':id' => $usuario['id']]);
    }

    session_regenerate_id(true);
    unset($_SESSION['star_tentativas'], $_SESSION['star_tentativas_inicio']);
    $_SESSION['star_usuario_id'] = (int) $usuario['id'];
    $_SESSION['star_usuario_nome'] = $usuario['nome'];
    $_SESSION['star_csrf'] = bin2hex(random_bytes(32));
    responderJson(200, ['sucesso' => true, 'mensagem' => 'Login realizado com sucesso!']);
} catch (PDOException $e) {
    error_log('Star Fitness login: ' . $e->getMessage());
    responderJson(500, ['erro' => 'Falha ao acessar o banco de dados.']);
}
