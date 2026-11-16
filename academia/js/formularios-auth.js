// Usado pelas páginas login.html e cadastro.html; preserva o layout existente.
(function () {
  const formulario = document.querySelector('form[data-auth]');
  if (!formulario) return;
  const aviso = document.getElementById('mensagem-formulario');
  const botao = formulario.querySelector('button[type="submit"]');
  const textoOriginal = botao.textContent;

  async function obterToken() {
    const resposta = await fetch('api/sessao.php', {
      credentials: 'same-origin',
      cache: 'no-store'
    });
    if (!resposta.ok) throw new Error('Não foi possível iniciar a sessão.');
    const dados = await resposta.json();
    return dados.csrf;
  }

  formulario.addEventListener('submit', async function (evento) {
    evento.preventDefault();
    if (!formulario.reportValidity()) return;
    aviso.textContent = '';
    aviso.hidden = true;
    botao.disabled = true;
    botao.textContent = 'Aguarde...';

    try {
      const token = await obterToken();
      const dados = new FormData(formulario);
      dados.set('csrf', token);
      const resposta = await fetch(formulario.action, {
        method: 'POST',
        body: dados,
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' }
      });
      const resultado = await resposta.json().catch(() => ({}));
      if (!resposta.ok) {
        throw new Error(resultado.erro || 'Não foi possível concluir a operação.');
      }
      window.location.assign('minha-conta.php');
    } catch (erro) {
      aviso.textContent = erro.message || 'Ocorreu um erro. Tente novamente.';
      aviso.hidden = false;
    } finally {
      botao.disabled = false;
      botao.textContent = textoOriginal;
    }
  });
})();
