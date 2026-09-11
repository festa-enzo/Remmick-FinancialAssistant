import { converterNumero } from './Converters.js';
const incomeForm = document.getElementById('income-form');
const incomeFilterMonth = document.getElementById('filter-month');
const incomeFilterYear = document.getElementById('filter-year');
const incomeList = document.getElementById('incomes-list');
const monthIncomes = document.getElementById('month-incomes');
const receivedIncomes = document.getElementById('received-incomes');
const pendingIncomes = document.getElementById('pending-incomes');
const notes = document.querySelector('.notes');
const saveNoteButton = document.getElementById('save-note');
let noteId = null;
let editingIncomeId = null;

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

if (incomeForm) {

    incomeForm.addEventListener('submit', async (e) => {

        e.preventDefault();

        const formData = new FormData(incomeForm);
        const objectForm = Object.fromEntries(formData);

        const method = editingIncomeId === null ? 'POST' : 'PUT';

        let url;

        if (editingIncomeId === null) {

            url = 'http://api.remmick.com/api/income';

        } else {

            url = `http://api.remmick.com/api/income/${editingIncomeId}`;

        }

        try {

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
                body: JSON.stringify(objectForm)
            });

            const responseData = await response.json();
            if (!response.ok) {
                alert(responseData.message);
                return;
            }

            if (responseData.success) {
                alert(
                    editingIncomeId === null
                        ? 'Receita criada com sucesso!'
                        : 'Receita alterada com sucesso!'
                );

                editingIncomeId = null;

                incomeForm.reset();
                document.getElementById('income-year').value = '2026';
                document.getElementById('submit-income').textContent =
                    'Adicionar receita';

                buscarReceitas();

            }

        } catch (error) {
            alert('Erro de conexão com o servidor');
        }
    });
}

async function buscarReceitas() {

    const month = incomeFilterMonth.value;
    const year = incomeFilterYear.value;

    incomeFilterMonth.addEventListener('change', buscarReceitas);
    incomeFilterYear.addEventListener('change', buscarReceitas);

    if (year === "") {

        alert("Selecione o ano");
        return;

    }

    const params = new URLSearchParams({
        income_year: year
    });

    let response;

    if (month !== "") {
        params.set("income_month_id", month);
        response = await fetch(
            'http://api.remmick.com/api/income/month?' + params,
            {
                method: 'GET',
                headers: {
                    'Authorization':
                        'Bearer ' + localStorage.getItem('token')
                }
            }
        );
    } else {
        response = await fetch(
            'http://api.remmick.com/api/income/year?' + params,
            {
                method: 'GET',
                headers: {
                    'Authorization':
                        'Bearer ' + localStorage.getItem('token')
                }
            }
        );
    }

    const responseData = await response.json();

    if (!response.ok) {
        alert(responseData.message);
        return;
    }

    if (responseData.success) {

        const incomeData = responseData.data;

        incomeList.innerHTML = '';

        let totalReceitas = 0;
        let totalRecebidas = 0;
        let totalPendentes = 0;

        if (incomeData === null || incomeData.length === 0) {
            incomeList.innerHTML = `
                <div class="entry">
                    <div class="entry-info">
                        <div class="entry-icon">-</div>

                        <div>
                            <strong>Nenhuma receita encontrada</strong>
                            <span>Não existem receitas para este período.</span>
                        </div>
                    </div>
                </div>
            `;

            monthIncomes.textContent = "R$ 0,00";
            receivedIncomes.textContent = "R$ 0,00";
            pendingIncomes.textContent = "R$ 0,00";

            return;
        }

        incomeData.forEach((income) => {

            const entry = document.createElement('div');
            entry.classList.add('entry');

            const entryInfo = document.createElement('div');
            entryInfo.classList.add('entry-info');

            const entryIcon = document.createElement('div');
            entryIcon.classList.add('entry-icon');

            const infoContainer = document.createElement('div');

            const title = document.createElement('strong');
            title.textContent = income.title;

            const details = document.createElement('span');

            const typeName = converterNumero(
                "income_type",
                income.income_type_id
            );

            const originName = converterNumero(
                "income_origin",
                income.income_origin_id
            );

            details.textContent =
                typeName + ' • ' + originName;


            infoContainer.appendChild(title);
            infoContainer.appendChild(details);
            entryInfo.appendChild(entryIcon);
            entryInfo.appendChild(infoContainer);

            const entryValue = document.createElement('span');
            entryValue.classList.add('entry-value');

            entryValue.textContent = 'R$ ' + income.value;


            const statusName = converterNumero(
                "is_received",
                income.is_received
            );

            if (income.is_received == 0) {

                entryValue.classList.add('pending');

            }

            entryValue.textContent =
                'R$ ' + income.value + ' • ' + statusName;

            const actionsContainer = document.createElement('div');

            actionsContainer.classList.add('table-actions');


            const editButton = document.createElement('button');

            editButton.classList.add('btn-secondary');

            editButton.textContent = 'Editar';


            editButton.addEventListener('click', () => {

                editingIncomeId = income.id;
                document.getElementById('income-name').value =
                    income.title;
                document.getElementById('income-value').value =
                    income.value;
                document.getElementById('income-month').value =
                    income.income_month_id;
                document.getElementById('income-year').value =
                    income.income_year;
                document.getElementById('income-type').value =
                    income.income_type_id;
                document.getElementById('income-source').value =
                    income.income_origin_id;
                document.getElementById('income-status').value =
                    income.is_received;
                document.getElementById('submit-income').textContent =
                    'Salvar alterações';
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

            });
            const deleteButton = document.createElement('button');

            deleteButton.classList.add('btn-delete');

            deleteButton.textContent = 'Excluir';


            deleteButton.addEventListener('click', async () => {

                const response = await fetch(
                    'http://api.remmick.com/api/income/' + income.id,
                    {
                        method: 'DELETE',

                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization':
                                'Bearer ' +
                                localStorage.getItem('token')
                        }
                    }
                );

                const responseData = await response.json();

                if (!response.ok) {

                    alert(responseData.message);
                    return;

                }

                if (responseData.success) {

                    buscarReceitas();

                }

            });

            actionsContainer.appendChild(editButton);
            actionsContainer.appendChild(deleteButton);
            entry.appendChild(entryInfo);
            entry.appendChild(entryValue);
            entry.appendChild(actionsContainer);

            incomeList.appendChild(entry);

            totalReceitas += Number(income.value);

            if (income.is_received == 1) {

                totalRecebidas += Number(income.value);

            } else {

                totalPendentes += Number(income.value);
            }
        });

        monthIncomes.textContent =
            'R$ ' + totalReceitas.toFixed(2);

        receivedIncomes.textContent =
            'R$ ' + totalRecebidas.toFixed(2);

        pendingIncomes.textContent =
            'R$ ' + totalPendentes.toFixed(2);

    }

}
async function buscarNota() {

    const response = await fetch(
        'http://api.remmick.com/api/income/note',
        {
            method: 'GET',
            headers: {
                'Authorization':
                    'Bearer ' + localStorage.getItem('token')
            }
        }
    );

    const responseData = await response.json();

    if (!response.ok) {
        alert(responseData.message);
        return;
    }

    if (responseData.success) {

        if (responseData.data === null) {

            noteId = null;
            notes.value = '';

        } else {

            noteId = responseData.data.id;
            notes.value = responseData.data.content;

        }
    }
}
saveNoteButton.addEventListener('click', async () => {

    const objectNote = {
        content: notes.value
    };

    const method = noteId === null ? 'POST' : 'PUT';

    let url;

    if (noteId === null) {
        url = 'http://api.remmick.com/api/income/note';
    } else {
        url = `http://api.remmick.com/api/income/note/${noteId}`;
    }

    try {

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Authorization':
                    'Bearer ' + localStorage.getItem('token')
            },
            body: JSON.stringify(objectNote)
        });

        const responseData = await response.json();

        if (!response.ok) {
            alert(responseData.message);
            return;
        }

        if (responseData.success) {

            const mensagem = noteId === null
                ? 'Nota criada com sucesso!'
                : 'Nota alterada com sucesso!';

            if (noteId === null) {
                noteId = responseData.data;
            }

            alert(mensagem);
        }
    } catch (error) {

        alert('Erro de conexão com o servidor');

    }

});

document.addEventListener(
    "DOMContentLoaded",
    () => {
        buscarReceitas();
        buscarNota();
    }
);

incomeFilterMonth.addEventListener(
    "change",
    buscarReceitas
);
