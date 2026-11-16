<?php
declare(strict_types=1);
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/config/conexao.php';

if (!isset($_SESSION['star_usuario_id'])) {
    header('Location: login.html', true, 302);
    exit;
}

try {
    $pdo = conectarBanco();
    $consulta = $pdo->prepare(
        'SELECT nome, email, telefone, data_nascimento FROM contas_star_fitness WHERE id = :id LIMIT 1'
    );
    $consulta->execute([':id' => $_SESSION['star_usuario_id']]);
    $cliente = $consulta->fetch();
} catch (PDOException $e) {
    error_log('Star Fitness minha-conta: ' . $e->getMessage());
    http_response_code(503);
    exit('Não foi possível carregar a conta. Verifique se o MySQL está ligado.');
}

if (!$cliente) {
    unset($_SESSION['star_usuario_id'], $_SESSION['star_usuario_nome']);
    header('Location: login.html', true, 302);
    exit;
}

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Minha conta | Star Fitness</title>
<style>
:root{--bg:#0b0a12;--panel:#15131f;--ink:#fff;--muted:#a9a4bd;--line:#2c2840;--p2:#a855f7;--p3:#7c3aed}
*{box-sizing:border-box}
body{margin:0;min-height:100vh;display:flex;flex-direction:column;align-items:center;gap:24px;padding:28px 20px 40px;background:var(--bg);color:var(--ink);font:400 16px/1.5 Arial,Helvetica,sans-serif}
.logo{font-size:1.7rem;font-weight:700;color:var(--ink);text-decoration:none}
.logo span{color:var(--p2)}
main{width:100%;max-width:460px;background:var(--panel);border:1px solid var(--line);border-radius:16px;padding:30px 26px}
h1{font-size:1.6rem;margin:0 0 6px}
.sub{color:var(--muted);margin:0 0 24px}
.info{border-bottom:1px solid var(--line);padding:11px 0;overflow-wrap:anywhere}
.info small{display:block;color:var(--muted);font-size:.85rem}
.btn,button{display:block;width:100%;margin-top:18px;padding:13px;border:0;border-radius:10px;background:var(--p3);color:white;text-align:center;text-decoration:none;font:700 1rem Arial,Helvetica,sans-serif;cursor:pointer}
button{background:#2b2455;margin-top:10px}
button:hover,.btn:hover{background:var(--p2)}
:focus-visible{outline:3px solid var(--p2);outline-offset:3px}
</style>
</head>
<body>
<a class="logo" href="Academia.html">Star <span>Fitness</span></a>
<main>
  <h1>Minha conta</h1>
  <p class="sub">Você está conectado à sua conta Star Fitness.</p>
  <div class="info"><small>Nome</small><?= h($cliente['nome']) ?></div>
  <div class="info"><small>E-mail</small><?= h($cliente['email']) ?></div>
  <div class="info"><small>Telefone</small><?= h($cliente['telefone']) ?></div>
  <div class="info"><small>Data de nascimento</small><?= h(date('d/m/Y', strtotime($cliente['data_nascimento']))) ?></div>
  <a class="btn" href="Academia.html">Voltar à Star Fitness</a>
  <form method="post" action="api/sair.php">
    <input type="hidden" name="csrf" value="<?= h($_SESSION['star_csrf']) ?>">
    <button type="submit">Sair da conta</button>
  </form>
</main>
</body>
</html>
