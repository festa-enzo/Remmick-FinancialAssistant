import { converterNumero } from './converters.js';
const expenseForm = document.getElementById('expense-form');
const expenseMessage = document.getElementById('expense-form-message');
const expenseFilterMonth = document.getElementById("filter-month");
const expenseFilterYear = document.getElementById("filter-year");
const expenseTableBody = document.getElementById("expenses-table-body")

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
})}
    async function buscarGastos() {

    const month = expenseFilterMonth.value;
    const year = expenseFilterYear.value;

    if (month === "" || year === "") {
    alert("Selecione o mês e o ano");
    return;
    } 

    const params = new URLSearchParams({
        expense_month_id: month,
        expense_year: year
    });

    const response = await fetch(
    'http://api.remmick.com/api/expense?' + params,
    {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        }
    }
    );

    const responseData = await response.json();

    if (!response.ok) {
    alert(responseData.message);
    return;
    }
    if (responseData.success) {
        const expenseData = responseData.data;

        expenseData.forEach((expense) => {
            
            const row = document.createElement('tr');
            const titleCell = document.createElement('td');
            const categoryCell = document.createElement('td');
            const institutionCell = document.createElement('td');
            const methodCell = document.createElement('td');


            const categoryName = converterNumero("categories", expense.category_id);
            const institutionName = converterNumero("institution", expense.institution_id);
            const methodName = converterNumero("method", expense.method_id);

            titleCell.textContent = expense.title;
            categoryCell.textContent = categoryName;
            institutionCell.textContent = institutionName;
            methodCell.textContent = methodName;

            row.appendChild(titleCell);
            row.appendChild(categoryCell);
            row.appendChild(institutionCell);
            row.appendChild(methodCell);
            expenseTableBody.appendChild(row);

        });
    }
};


