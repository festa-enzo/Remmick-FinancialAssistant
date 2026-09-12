import { converterNumero } from './Converters.js';
import { apiFetch } from './Api.js';
const expenseForm = document.getElementById('expense-form');
const expenseMessage = document.getElementById('expense-form-message');
const expenseFilterMonth = document.getElementById("filter-month");
const expenseFilterYear = document.getElementById("filter-year");
const expenseTableBody = document.getElementById("expenses-table-body");
const emptyRow = document.querySelector(".empty-row");
const monthExpenses = document.getElementById("month-expenses");
const paidExpenses = document.getElementById("paid-expenses");
const pendingExpenses = document.getElementById("pending-expenses");
let editingExpenseId = null;
let deletingExpenseId = null;

const userNameElement = document.getElementById("user-name");

try {
    const user = JSON.parse(localStorage.getItem("user")) || {};

    if (userNameElement) {
        userNameElement.textContent = user.name || "Usuário";
    }
} catch {
    if (userNameElement) {
        userNameElement.textContent = "Usuário";
    }
}

if (expenseForm) {
    expenseForm.addEventListener('submit', async (e) => {
    e.preventDefault()
        
    const formData = new FormData(expenseForm);
    const objectForm = Object.fromEntries(formData);
    const method = editingExpenseId === null ? 'POST' : 'PUT';
    let url;

    if (editingExpenseId === null) {
        url = 'http://api.remmick.com/api/expense';
    } else {
        url = `http://api.remmick.com/api/expense/${editingExpenseId}`;
    }
      
    try {
    const response = await apiFetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
         },
        body: JSON.stringify(objectForm)
    })    

    if (!response) return;

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

    if (year === "") {
    alert("Selecione o ano");
    return;
    } 

    const params = new URLSearchParams({
        expense_year: year
    });

    let response;

    if(month !== ""){
        params.set("expense_month_id", month)
        response = await apiFetch(
            'http://api.remmick.com/api/expense/month?' + params,
        {
            method: 'GET',
        }
        );
    } else {
        response = await apiFetch(
            'http://api.remmick.com/api/expense/year?' + params,
        {
            method: 'GET',
        }
        );        
    }

    if (!response) return;

    const responseData = await response.json();

    if (!response.ok) {
    alert(responseData.message);
    return;
    }
    if (responseData.success) {
        const expenseData = responseData.data;

    if (expenseData === null) {
        emptyRow.style.display = "";
        return;
    }
        const expenseRows = document.querySelectorAll("tr:not(.empty-row)");

        expenseRows.forEach((row) => {
            row.remove();
        });


        emptyRow.style.display = "none";      
        
        let totalGastos = 0;
        let totalPagos = 0;
        let totalPendentes = 0;

        expenseData.forEach((expense) => {
            const row = document.createElement('tr');
            const titleCell = document.createElement('td');
            const categoryCell = document.createElement('td');
            const institutionCell = document.createElement('td');
            const methodCell = document.createElement('td');
            const valueCell = document.createElement('td');
            const statusCell = document.createElement('td');   
            const actionsCell = document.createElement('td');   
            
            const actionsContainer = document.createElement('div');
            actionsContainer.classList.add('table-actions');

            const editButton = document.createElement('button');
            editButton.classList.add('action-button');

            const deleteButton = document.createElement('button');
            deleteButton.classList.add('action-button', 'delete');

            const categoryName = converterNumero("categories", expense.category_id);
            const institutionName = converterNumero("institution", expense.institution_id);
            const methodName = converterNumero("method", expense.method_id);
            const statusName = converterNumero("is_paid", expense.is_paid);

            titleCell.textContent = expense.title;
            categoryCell.textContent = categoryName;
            institutionCell.textContent = institutionName;
            methodCell.textContent = methodName;
            valueCell.textContent = "R$" + expense.value;
            statusCell.textContent = statusName;


            editButton.textContent = 'Editar';
            editButton.dataset.expenseId = expense.id;
            
            editButton.addEventListener('click', () => {
                editingExpenseId = editButton.dataset.expenseId;

                document.getElementById('expense-title').value = expense.title;
                document.getElementById('expense-value').value = expense.value;
                document.getElementById('expense-month').value = expense.expense_month_id;
                document.getElementById('expense-year').value = expense.expense_year;
                document.getElementById('expense-category').value = expense.category_id;
                document.getElementById('expense-institution').value = expense.institution_id;
                document.getElementById('expense-method').value = expense.method_id;

                    document.getElementById('submit-expense').textContent = 'Salvar alterações';
                    document.getElementById('expense-paid-group').style.display = '';
            });


            deleteButton.textContent = 'Excluir';
            deleteButton.dataset.expenseId = expense.id;

            deleteButton.addEventListener('click', async () => {

                const response = await apiFetch(
                    'http://api.remmick.com/api/expense/' + expense.id,
                    {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    }
                );
                if (!response) return;

                const responseData = await response.json();

                if (!response.ok) {
                    alert(responseData.message);
                    return;
                }

            });

            totalGastos += Number(expense.value);

            if (expense.is_paid === 0) {
                totalPendentes += Number(expense.value)
            } else {
                totalPagos += Number(expense.value)
            }


            row.appendChild(titleCell);
            row.appendChild(categoryCell);
            row.appendChild(institutionCell);
            row.appendChild(methodCell);
            row.appendChild(valueCell);
            row.appendChild(statusCell);
            row.appendChild(actionsCell);

            actionsCell.appendChild(actionsContainer);
            actionsContainer.appendChild(editButton);
            actionsContainer.appendChild(deleteButton);
            expenseTableBody.appendChild(row);

        });

        monthExpenses.textContent = "R$" + totalGastos;

        paidExpenses.textContent = "R$" + totalPagos

        pendingExpenses.textContent = "R$" + totalPendentes 
    }
}


    document.addEventListener("DOMContentLoaded", buscarGastos);

    expenseFilterMonth.addEventListener("change", buscarGastos);

    expenseFilterYear.addEventListener("change", buscarGastos);



