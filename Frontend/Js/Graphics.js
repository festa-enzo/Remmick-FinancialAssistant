const totalIncome = document.getElementById('month-incomes');
const totalExpense = document.getElementById('month-expenses');
const balance = document.getElementById('month-balance');
const incomeBars = document.querySelectorAll('.income-bar');
const expenseBars = document.querySelectorAll('.expense-bar');
const graphicsPeriod = document.getElementById('graphics-period');


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

graphicsPeriod.addEventListener('change', buscarReceitasMensais);
graphicsPeriod.addEventListener('change', buscarResumoFinanceiro);
graphicsPeriod.addEventListener('change', buscarGastosMensais);

document.addEventListener('DOMContentLoaded', buscarGastosMensais);
document.addEventListener('DOMContentLoaded', buscarReceitasMensais);
document.addEventListener('DOMContentLoaded', buscarResumoFinanceiro);