
document.addEventListener("DOMContentLoaded", async () => {
    const login = document.getElementById("link-login");
    const cadastro = document.getElementById("link-cadastro");

    if (!login || !cadastro) return;

    try {
        const resposta = await fetch("api/sessao.php", {
            credentials: "same-origin",
            cache: "no-store"
        });

        if (!resposta.ok) {
            throw new Error("Não foi possível consultar a sessão");
        }

        const dados = await resposta.json();

        if (dados.autenticado === true) {
            login.textContent = "Minha conta";
            login.href = "minha-conta.php";

            cadastro.style.display = "none";
        } else {
            login.textContent = "Login";
            login.href = "login.html";

            cadastro.style.display = "";
        }

    } catch (erro) {
        console.error("Erro ao verificar sessão:", erro);
    }
});
