const form = document.getElementById('LoginForm');
const mensage = document.getElementById('mensage');

const token = localStorage.getItem("token"); 

if (form) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();

        mensage.textContent = '';
        mensage.style.color = 'red';

        // Validação básica no frontend
        if (!email || !password) {
            mensage.textContent = 'Por favor, preencha todos os campos.';
            return;
        }

        try {
            const response = await fetch('http://api.remmick.com/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
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

                mensage.style.color = 'green';
                mensage.textContent = 'Login realizado! Redirecionando...';

                setTimeout(() => {
                    window.location.href = 'Home.html';   
                }, 1200);
            } else {
                mensage.textContent = data.message || 'Email ou senha incorretos.';
            }
        } catch (error) {
            mensage.textContent = 'Erro de conexão com o servidor. Verifique se o backend está rodando.';
            console.error('Erro:', error);
        }
    });
}