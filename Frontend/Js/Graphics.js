import { converterNumero } from './Converters.js';
const totalIncome = document.getElementById('month-incomes');
const totalExpense = document.getElementById('month-expenses');
const balance = document.getElementById('month-balance');
const incomeBars = document.querySelectorAll('.income-bar');
const expenseBars = document.querySelectorAll('.expense-bar');
const graphicsPeriod = document.getElementById('graphics-period');
const categoryBars = document.querySelectorAll('.category-fill');
const categoryValues = document.querySelectorAll('.category-value');
const donutChart = document.querySelector('.donut-chart');
const donutTotal = document.querySelector('.donut-center strong');
const distributionLegend = document.querySelector('.distribution-legend');
const expenseList = document.querySelector(
    '.financial-list-card:first-child .financial-list'
);
const incomeList = document.querySelector(
    '.financial-list-card:nth-child(2) .financial-list'
);


async function buscarResumoFinanceiro() {

    const year = graphicsPeriod.value;

    const response = await fetch(
        `http://api.remmick.com/api/graphics/summary?year=${year}`,
        {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }
    );

    const result = await response.json();

    console.log(result);

    if (!result.success) {
        console.error(result.message);
        return;
    }

    totalIncome.textContent = `R$ ${result.data.total_income}`;
    totalExpense.textContent = `R$ ${result.data.total_expense}`;
    balance.textContent = `R$ ${result.data.balance}`;
}

async function buscarReceitasMensais() {

    const year = graphicsPeriod.value;

    const response = await fetch(
        `http://api.remmick.com/api/graphics/month-income?year=${year}`,
        {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }
    );

    const result = await response.json();

    console.log(result);

    if (!result.success) {
        console.error(result.message);
        return;
    }

    const valores = Object.values(result.data);
    const maiorValor = Math.max(...valores);

    incomeBars.forEach(bar => {

        const month = bar.dataset.month;
        const value = result.data[month];

        const altura = (value / 4000) * 110;

        bar.style.height = `${altura}%`;
    });
}

async function buscarGastosMensais() {

    const year = graphicsPeriod.value;

    const response = await fetch(
        `http://api.remmick.com/api/graphics/month-expense?year=${year}`,
        {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }
    );

    const result = await response.json();

    console.log(result);

    if (!result.success) {
        console.error(result.message);
        return;
    }

    expenseBars.forEach(bar => {

        const month = bar.dataset.month;
        const value = result.data[month];

        const altura = (value / 4000) * 110;

        bar.style.height = `${altura}%`;
    });
}

async function buscarGastosPorCategoria() {
    const year = graphicsPeriod.value;

    const response = await fetch(
        `http://api.remmick.com/api/graphics/expense-category?year=${year}`,
        {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }
    );

    const result = await response.json();

    console.log(result);

    if (!result.success) {
        console.error(result.message);
        return;
    }

    const valores = Object.values(result.data).map(Number);
    const maiorValor = Math.max(...valores, 0);

    categoryBars.forEach((bar, index) => {
        const category = bar.dataset.category;

        const value = Number(result.data[category] || 0);

        const porcentagem = maiorValor > 0
            ? (value / maiorValor) * 100
            : 0;

        bar.style.width = `${porcentagem}%`;

        categoryValues[index].textContent =
            `R$ ${value.toFixed(2).replace('.', ',')}`;
    });
}

async function buscarDistribuicaoGastos() {
    const year = graphicsPeriod.value;

    const response = await fetch(
        `http://api.remmick.com/api/graphics/expense-category?year=${year}`,
        {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }
    );

    const result = await response.json();

    console.log(result);

    if (!result.success) {
        console.error(result.message);
        return;
    }

    const dados = Object.entries(result.data).map(([category, value]) => ({
        category,
        value: Number(value)
    }));

    const total = dados.reduce(
        (soma, item) => soma + item.value,
        0
    );

    donutTotal.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;

    if (total === 0) {
        donutChart.style.background = '#f1f3f8';
        distributionLegend.innerHTML = '';
        return;
    }

    const cores = [
        '#6366f1',
        '#8b5cf6',
        '#a78bfa',
        '#c4b5fd'
    ];

    let grauAtual = 0;
    const segmentos = [];

    dados.forEach((item, index) => {
        const porcentagem = (item.value / total) * 100;
        const graus = porcentagem * 3.6;

        segmentos.push(
            `${cores[index % cores.length]} ${grauAtual}deg ${grauAtual + graus}deg`
        );

        grauAtual += graus;
    });

    donutChart.style.background = `
        conic-gradient(
            ${segmentos.join(', ')}
        )
    `;

    distributionLegend.innerHTML = '';

    dados.forEach((item, index) => {
        const porcentagem = (item.value / total) * 100;

        const legenda = document.createElement('div');

        legenda.className = 'distribution-item';

        const nomeCategoria = converterNumero(
            'categories',
            item.category
        );

        legenda.innerHTML = `
            <span
                class="distribution-color"
                style="background: ${cores[index % cores.length]}"
            ></span>

            <span>${nomeCategoria}</span>

            <strong>${porcentagem.toFixed(1)}%</strong>
        `;

                distributionLegend.appendChild(legenda);
            });
        }

async function buscarPrincipaisGastos() {
    const year = graphicsPeriod.value;

    const response = await fetch(
        `http://api.remmick.com/api/expense/year?expense_year=${year}`,
        {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }
    );

    const result = await response.json();

    console.log('Gastos:', result);

    if (!result.success) {
        console.error(result.message);
        return;
    }

    const gastos = result.data;

    expenseList.innerHTML = '';

    if (!gastos || gastos.length === 0) {
        expenseList.innerHTML = `
            <div class="financial-item">
                <div class="financial-icon">
                    -
                </div>

                <div class="financial-info">
                    <strong>Nenhum gasto encontrado</strong>
                    <span>Seus gastos aparecerão aqui.</span>
                </div>

                <strong class="financial-value">
                    R$ 0,00
                </strong>
            </div>
        `;

        return;
    }

    const principaisGastos = [...gastos]
        .sort((a, b) => Number(b.value) - Number(a.value))
        .slice(0, 5);

    principaisGastos.forEach(gasto => {
        const nomeCategoria = converterNumero(
            'categories',
            gasto.category_id
        );

        const item = document.createElement('div');

        item.className = 'financial-item';

        item.innerHTML = `
            <div class="financial-icon">
                -
            </div>

            <div class="financial-info">
                <strong>${nomeCategoria || 'Categoria'}</strong>
                <span>Despesa do período</span>
            </div>

            <strong class="financial-value">
                R$ ${Number(gasto.value).toFixed(2).replace('.', ',')}
            </strong>
        `;

        expenseList.appendChild(item);
    });
}

    async function buscarPrincipaisReceitas() {
    const year = graphicsPeriod.value;

    const response = await fetch(
        `http://api.remmick.com/api/income/year?income_year=${year}`,
        {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }
    );

    const result = await response.json();

    console.log('Receitas:', result);

    if (!result.success) {
        console.error(result.message);
        return;
    }

    const receitas = result.data;

    incomeList.innerHTML = '';

    if (!receitas || receitas.length === 0) {
        incomeList.innerHTML = `
            <div class="financial-item">
                <div class="financial-icon income-icon">
                    +
                </div>

                <div class="financial-info">
                    <strong>Nenhuma receita encontrada</strong>
                    <span>Suas receitas aparecerão aqui.</span>
                </div>

                <strong class="financial-value income-value">
                    R$ 0,00
                </strong>
            </div>
        `;

        return;
    }

    const principaisReceitas = [...receitas]
        .sort((a, b) => Number(b.value) - Number(a.value))
        .slice(0, 5);

    principaisReceitas.forEach(receita => {
        const nomeOrigem = converterNumero(
            'income_origin',
            receita.income_origin_id
        );

        const item = document.createElement('div');

        item.className = 'financial-item';

        item.innerHTML = `
            <div class="financial-icon income-icon">
                +
            </div>

            <div class="financial-info">
                <strong>${nomeOrigem || 'Receita'}</strong>
                <span>Entrada do período</span>
            </div>

            <strong class="financial-value income-value">
                R$ ${Number(receita.value).toFixed(2).replace('.', ',')}
            </strong>
        `;

        incomeList.appendChild(item);
    });
}

graphicsPeriod.addEventListener('change', buscarReceitasMensais);
graphicsPeriod.addEventListener('change', buscarResumoFinanceiro);
graphicsPeriod.addEventListener('change', buscarGastosMensais);
graphicsPeriod.addEventListener('change', buscarGastosPorCategoria);
graphicsPeriod.addEventListener('change', buscarDistribuicaoGastos);
graphicsPeriod.addEventListener('change', buscarPrincipaisGastos);
graphicsPeriod.addEventListener('change', buscarPrincipaisReceitas);

document.addEventListener('DOMContentLoaded', buscarGastosMensais);
document.addEventListener('DOMContentLoaded', buscarReceitasMensais);
document.addEventListener('DOMContentLoaded', buscarResumoFinanceiro);
document.addEventListener('DOMContentLoaded', buscarGastosPorCategoria);
document.addEventListener('DOMContentLoaded', buscarDistribuicaoGastos);
document.addEventListener('DOMContentLoaded', buscarPrincipaisGastos);
document.addEventListener('DOMContentLoaded', buscarPrincipaisReceitas);