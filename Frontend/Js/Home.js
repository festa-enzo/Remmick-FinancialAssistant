import { converterNumero } from './Converters.js';
import { apiFetch } from './Api.js';
const API_URL = 'http://api.remmick.com/api';
const userName = document.getElementById('user-name');
const currentBalance = document.getElementById('current-balance');
const currentIncome = document.getElementById('current-income');
const currentExpenses = document.getElementById('current-expenses');
const latestExpensesList = document.getElementById('latest-expenses-list');
const chartPeriod = document.getElementById('chart-period');
const financialChart = document.getElementById('financial-chart');
const summaryYear = document.getElementById('summary-year');
const monthlySummaryBody = document.getElementById('monthly-summary-body');

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

function formatCurrency(value) {

    return Number(value || 0).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
}
async function apiGetJson(url) {
    const response = await apiFetch(url);

    if (!response) return null;

    const responseData = await response.json();

    if (!response.ok) {
        throw new Error(
            responseData.message || 'Erro ao consultar a API'
        );
    }

    return responseData;
}

function getCurrentDate() {
    return new Date();
}

function getCurrentYear() {
    return getCurrentDate().getFullYear();
}

function getCurrentMonth() {
    return getCurrentDate().getMonth() + 1;
}

function carregarUsuario() {
    const userStorage =
        localStorage.getItem('user');

    if (!userStorage) {
        return;
    }

    try {
        const user =
            JSON.parse(userStorage);

        if (user.name) {
            userName.textContent =
                user.name;
        }
    } catch (error) {
        console.error(
            'Erro ao carregar usuário:',
            error
        );
    }
}

async function buscarReceitasMes(year, month) {
    const params = new URLSearchParams({
            income_year: year,
            income_month_id: month
        });

    const responseData = await apiGetJson(`${API_URL}/income/month?${params}`);

    if (!responseData) {
        return [];
    }

    return responseData.data || [];
}

async function buscarGastosMes(year, month) {
    const params =
        new URLSearchParams({
            expense_year: year,
            expense_month_id: month
        });

    const responseData =
        await apiGetJson(`${API_URL}/expense/month?${params}`);

    if (!responseData) {
        return [];
    }
    return responseData.data || [];
}


function calcularTotalReceitas(receitas) {
    let total = 0;

    receitas.forEach((income) => {
        total += Number(income.value);
    });
    return total;
}

function calcularTotalGastos(gastos) {
    let total = 0;

    gastos.forEach((expense) => {
        total += Number(expense.value);
    });
    return total;
}

async function carregarResumoAtual() {
    const year = Number(summaryYear.value);
    if (!year) {
        return;
    }
    try {
        const [responseReceitas, responseGastos] = await Promise.all([
            apiGetJson(`${API_URL}/income/year?income_year=${year}`),
            apiGetJson(`${API_URL}/expense/year?expense_year=${year}`)
        ]);

        if (!responseReceitas || !responseGastos) {
            return;
        }

        const receitas = responseReceitas.data || [];
        const gastos = responseGastos.data || [];

        const totalReceitas = calcularTotalReceitas(receitas);
        const totalGastos = calcularTotalGastos(gastos);
        const saldo = totalReceitas - totalGastos;

        currentIncome.textContent = formatCurrency(totalReceitas);
        currentExpenses.textContent = formatCurrency(totalGastos);
        currentBalance.textContent = formatCurrency(saldo);

    } catch (error) {
        console.error('Erro ao carregar resumo atual:', error);
    }
}

async function carregarUltimosGastos() {
    try {
        const responseData = await apiGetJson(
            `${API_URL}/expense/latest`
        );
        if (!responseData) {
            return;
        }
        const gastos = responseData.data || [];

        latestExpensesList.innerHTML = '';

        if (gastos.length === 0) {
            latestExpensesList.innerHTML = `
                <div class="expense-item">
                    <div class="expense-icon">-</div>
                    <div class="expense-info">
                        <span class="expense-title">
                            Nenhum gasto encontrado
                        </span>
                        <span class="expense-date">
                            Nenhum lançamento encontrado
                        </span>
                    </div>
                    <div class="expense-value">
                        R$ 0,00
                    </div>
                </div>
            `;
            return;
        }

        gastos.forEach((expense) => {
            const expenseItem = document.createElement('div');
            expenseItem.classList.add('expense-item');

            const expenseIcon = document.createElement('div');
            expenseIcon.classList.add('expense-icon');

            const categoryName = converterNumero(
                'categories',
                expense.category_id
            );

            expenseIcon.textContent =
                categoryName
                    ? categoryName.charAt(0)
                    : '-';

            const expenseInfo = document.createElement('div');
            expenseInfo.classList.add('expense-info');

            const title = document.createElement('span');
            title.classList.add('expense-title');
            title.textContent = expense.title;

            const date = document.createElement('span');
            date.classList.add('expense-date');
            date.textContent = categoryName || 'Gasto';

            expenseInfo.appendChild(title);
            expenseInfo.appendChild(date);

            const value = document.createElement('div');
            value.classList.add('expense-value');
            value.textContent = formatCurrency(expense.value);

            expenseItem.appendChild(expenseIcon);
            expenseItem.appendChild(expenseInfo);
            expenseItem.appendChild(value);

            latestExpensesList.appendChild(expenseItem);
        });

    } catch (error) {
        console.error('Erro ao carregar últimos gastos:', error);
    }
}

function prepararAnoResumo() {
    summaryYear.innerHTML = "";

    for (let year = 2024; year <= 2035; year++) {
        const option = document.createElement("option");

        option.value = year;
        option.textContent = year;
        summaryYear.appendChild(option);
    }

    summaryYear.value = getCurrentYear();
}

async function buscarDadosGrafico() {

    const quantidadeMeses = Number(chartPeriod.value);
    const dataAtual = getCurrentDate();
    const resultados = [];

    for (
        let i = quantidadeMeses - 1;
        i >= 0;
        i--
    ) {

        const data =
            new Date(
                dataAtual.getFullYear(),
                dataAtual.getMonth() - i,
                1
            );


        const year = data.getFullYear();
        const month = data.getMonth() + 1;
        const [receitas, gastos] = await Promise.all([
                buscarReceitasMes(
                    year,
                    month
                ),
                buscarGastosMes(
                    year,
                    month
                )
            ]);


        resultados.push({
            month: month,
            year: year,
            label:
                converterNumero(
                    'months',
                    month
                ),
            income:
                calcularTotalReceitas(
                    receitas
                ),
            expense:
                calcularTotalGastos(
                    gastos
                )
        });
    }
    return resultados;

}

async function carregarGrafico() {
    try {
        const dados = await buscarDadosGrafico();
        const ctx = financialChart.getContext('2d');

        if (window.homeChart) {
            window.homeChart.destroy();
        }

        window.homeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels:
                        dados.map(item => item.label),

                    datasets: [
                        {
                            label: 'Ganhos',
                            data:
                                dados.map(
                                    item =>
                                        item.income
                                ),

                            borderWidth: 2,
                            tension: 0.3,
                            fill: false

                        },
                        {
                            label: 'Gastos',
                            data:
                                dados.map(
                                    item =>
                                        item.expense
                                ),
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false
                        }
                    ]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },

                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(
                                        value
                                    );
                                }
                            }
                        }
                    }
                }
            });


    } catch (error) {
        console.error(
            'Erro ao carregar gráfico:',
            error
        );
    }
}

async function carregarResumoMensal() {
    const year =
        Number(summaryYear.value);
    if (!year) {
        return;
    }

    monthlySummaryBody.innerHTML = '';

    try {

        for (let month = 1; month <= 12; month++) {
            const [receitas, gastos] =
                await Promise.all([
                    buscarReceitasMes(
                        year,
                        month
                    ),
                    buscarGastosMes(
                        year,
                        month
                    )
                ]);

            const totalReceitas = calcularTotalReceitas(receitas);
            const totalGastos = calcularTotalGastos(gastos);
            const saldo = totalReceitas - totalGastos;
            const row = document.createElement('tr');
            const monthCell = document.createElement('td');

            monthCell.textContent = converterNumero('months', month);

            const incomeCell = document.createElement('td');

            incomeCell.textContent = formatCurrency(totalReceitas);

            const expenseCell = document.createElement('td');

            expenseCell.textContent = formatCurrency(totalGastos);

            const balanceCell = document.createElement('td');

            balanceCell.textContent = formatCurrency(saldo);

            row.appendChild(monthCell);
            row.appendChild(incomeCell);
            row.appendChild(expenseCell);
            row.appendChild(balanceCell);

            monthlySummaryBody.appendChild(row);
        }
    } catch (error) {
        console.error(
            'Erro ao carregar resumo mensal:',
            error
        );

        monthlySummaryBody.innerHTML = `

            <tr>

                <td colspan="4">
                    Erro ao carregar resumo mensal.
                </td>

            </tr>

        `;

    }

}

if (chartPeriod) {
    chartPeriod.addEventListener(
        'change',
        carregarGrafico
    );
}

if (summaryYear) {
    summaryYear.addEventListener('change', () => {
        carregarResumoAtual();
        carregarResumoMensal();
    });
}

document.addEventListener('DOMContentLoaded', async () => {
    carregarUsuario();
    prepararAnoResumo();
    await carregarResumoAtual();
    await carregarUltimosGastos();
    await carregarGrafico();
    await carregarResumoMensal();
});

