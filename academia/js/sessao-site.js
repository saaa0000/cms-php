// Atualiza apenas os links do cabeçalho quando o cliente já está conectado.
// O controle real da sessão acontece no PHP, não no navegador.
(async function () {
  try {
    const resposta = await fetch('api/sessao.php', {
      credentials: 'same-origin',
      cache: 'no-store'
    });
    if (!resposta.ok) return;
    const sessao = await resposta.json();
    if (!sessao.autenticado) return;

    document.querySelectorAll('a[href="login.html"], a[href="login(1).html"]').forEach(link => {
      link.href = 'minha-conta.php';
      link.textContent = 'Minha conta';
    });
    document.querySelectorAll('a[href="cadastro.html"], a[href="cadastro(1).html"]').forEach(link => {
      link.hidden = true;
    });
  } catch (erro) {
    // Se a sessão não estiver acessível, os links originais continuam visíveis.
  }
})();
