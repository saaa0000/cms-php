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

$nome = trim((string) ($_POST['nome'] ?? ''));
$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$telefone = trim((string) ($_POST['telefone'] ?? ''));
$senha = (string) ($_POST['senha'] ?? '');
$nascimento = trim((string) ($_POST['nascimento'] ?? ''));

if ($nome === '' || (function_exists('mb_strlen') ? mb_strlen($nome) : strlen($nome)) > 120) {
    responderJson(422, ['erro' => 'Informe um nome válido (até 120 caracteres).']);
}
if (strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responderJson(422, ['erro' => 'Informe um e-mail válido.']);
}
if (strlen($telefone) < 8 || strlen($telefone) > 20 || !preg_match('/^[0-9()+.\-\s]+$/', $telefone)) {
    responderJson(422, ['erro' => 'Informe um telefone válido.']);
}
// O hash padrão do PHP usa bcrypt nesta instalação; evita truncamento após 72 bytes.
if (strlen($senha) < 8 || strlen($senha) > 72) {
    responderJson(422, ['erro' => 'A senha deve ter entre 8 e 72 caracteres (bytes).']);
}
$data = DateTimeImmutable::createFromFormat('!Y-m-d', $nascimento);
if (!$data || $data->format('Y-m-d') !== $nascimento || $data > new DateTimeImmutable('today')) {
    responderJson(422, ['erro' => 'Informe uma data de nascimento válida.']);
}

try {
    $pdo = conectarBanco();
    $stmt = $pdo->prepare(
        'INSERT INTO contas_star_fitness (nome, email, telefone, senha_hash, data_nascimento)
         VALUES (:nome, :email, :telefone, :senha_hash, :data_nascimento)'
    );
    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':telefone' => $telefone,
        ':senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
        ':data_nascimento' => $nascimento,
    ]);

    session_regenerate_id(true);
    $_SESSION['star_usuario_id'] = (int) $pdo->lastInsertId();
    $_SESSION['star_usuario_nome'] = $nome;
    $_SESSION['star_csrf'] = bin2hex(random_bytes(32));
    responderJson(201, ['sucesso' => true, 'mensagem' => 'Cadastro realizado com sucesso!']);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        responderJson(409, ['erro' => 'Este e-mail já está cadastrado. Faça login.']);
    }
    error_log('Star Fitness cadastro: ' . $e->getMessage());
    responderJson(500, ['erro' => 'Não foi possível cadastrar. Confira a conexão com o banco.']);
}
