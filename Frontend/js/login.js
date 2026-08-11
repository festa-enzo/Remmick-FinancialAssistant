const form = document.getElementById('LoginForm');
const mensagem = document.getElementById('mensagem');

const token = localStorage.getItem("token"); 

if (form) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        const senha = document.getElementById('senha').value.trim();

        mensagem.textContent = '';
        mensagem.style.color = 'red';

        // Validação básica no frontend
        if (!email || !senha) {
            mensagem.textContent = 'Por favor, preencha todos os campos.';
            return;
        }

        try {
            const response = await fetch('http://api.remmick.com/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, senha })
            });

            const data = await response.json();

            if (!response.ok) {
             alert(data.message);
             return;
            }

            console.log("Login realizado!", data);

            if (data.success) {
                localStorage.setItem("token", data.accessToken);
                console.log("Salvou:", localStorage.getItem("token"));
                localStorage.setItem('user', JSON.stringify(data.user));

                mensagem.style.color = 'green';
                mensagem.textContent = 'Login realizado! Redirecionando...';

                setTimeout(() => {
                    window.location.href = 'index.html';   
                }, 1200);
            } else {
                mensagem.textContent = data.message || 'Email ou senha incorretos.';
            }
        } catch (error) {
            mensagem.textContent = 'Erro de conexão com o servidor. Verifique se o backend está rodando.';
            console.error('Erro:', error);
        }
    });
}