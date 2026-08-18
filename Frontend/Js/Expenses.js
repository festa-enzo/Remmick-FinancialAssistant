const expenseForm = document.getElementById('expense-form');
const expenseMessage = document.getElementById('expense-form-message');

if (expenseForm) {
    expenseForm.addEventListener('submit', async (e) => {
    e.preventDefault()
        
    const formData = new FormData(expenseForm);

    const objectForm = Object.fromEntries(formData);
      
    try {
    const response = await fetch('http://api.remmick.com/api/expense', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': "Bearer " + localStorage.getItem('token')
         },
        body: JSON.stringify(objectForm)
    })            
    
    const responseData = await response.json();
    
    if (!response.ok) {
    alert(responseData.message);
    return;
    }
    
    if (responseData.success) {
        expenseMessage.style.color = '#10b981';
        expenseMessage.textContent = '✅ Gasto criado com sucesso!';
                
    } else {
        expenseMessage.textContent = responseData.message || 'Erro ao criar gasto';
    }
    } catch (error) {
        expenseMessage.textContent = 'Erro de conexão com o servidor';
    }
})};


