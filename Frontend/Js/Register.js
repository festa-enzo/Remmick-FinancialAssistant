const registerForm = document.getElementById('registerform');
const mensage = document.getElementById('mensage');

if (registerForm)
    registerForm.addEventListener('submit', async(event) => {
    event.preventDefault();

    const inputName = document.getElementById('name').value.trim();
    const inputEmail = document.getElementById('email').value.trim();
    const inputPassword = document.getElementById('password').value.trim();
    const inputConfirmPassword = document.getElementById('confirm_password').value.trim();

    if (!inputName || !inputEmail || !inputPassword || !inputConfirmPassword) {
//        const errorMensage = document.getElementById('mensage');
        mensage.textContent = 'Por favor, preencha todos os campos.';
        return;
    }
    if (inputPassword !== inputConfirmPassword) {
        mensage.textContent = 'As senhas não coincidem'
        return;
    }
    try {
    const data = {name: inputName, email: inputEmail, password: inputPassword}
    const response = await fetch('http://api.remmick.com/api/register', {
        method: 'POST',
        headers: {
             'Content-Type': 'application/json'
         },
        body: JSON.stringify(data)
    })            
    
    const responseData = await response.json();

    if (!response.ok) {
    alert(responseData.message);
    return;
    }

    if (responseData.success) {
        mensage.style.color = '#10b981';
        mensage.textContent = '✅ Cadastro realizado com sucesso! Redirecionando...';
                
        setTimeout(() => {
        window.location.href = 'Login.html';
        }, 1800);
    } else {
        mensage.textContent = responseData.message || 'Erro ao realizar cadastro';
    }
    } catch (error) {
        mensage.textContent = 'Erro de conexão com o servidor';
    }
});


