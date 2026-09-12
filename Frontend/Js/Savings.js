import { apiFetch } from './Api.js';
const newGoalButton = document.getElementById('new-goal-button');
const goalModal = document.getElementById('goal-modal');
const closeGoalModal = document.getElementById('close-goal-modal');
const cancelGoalModal = document.getElementById('cancel-goal-modal');
const createGoalButton = document.getElementById('create-goal-button');
const goalMessage = document.getElementById('goal-message');
const totalSavedElement = document.getElementById('total-saved');
const totalTargetElement = document.getElementById('total-target');
const overallProgressElement = document.getElementById('overall-progress');
const goalsList = document.getElementById('goals-list');
const goalCount = document.getElementById('goal-count');
const depositsList = document.getElementById('deposits-list');
const paceStatusTitle = document.getElementById('pace-status-title');
const paceStatusDescription = document.getElementById('pace-status-description');
const expectedAmountElement = document.getElementById('expected-amount');
const currentPaceAmountElement = document.getElementById('current-pace-amount');
const paceHighlightLabel = document.getElementById('pace-highlight-label');
const paceHighlightValue = document.getElementById('pace-highlight-value');
const monthlyRequiredElement = document.getElementById('monthly-required');
const lineChart = document.getElementById('line-chart');
const chartLabels = document.getElementById('chart-labels');
const chartYAxis = document.getElementById('chart-y-axis');
const monthlySavingInput = document.getElementById('monthly-saving');
const savingMonthsSelect = document.getElementById('saving-months');
const simulatorResult = document.getElementById('simulator-result');

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

let goals = [];
let editingGoalId = null;


/* =========================================================
   FORMATAÇÃO
========================================================= */

const currencyFormatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
});

function formatCurrency(value) {
    return currencyFormatter.format(
        Number(value) || 0
    );

}

function formatDate(date) {

    if (!date) {
        return 'Sem prazo definido';
    }

    const dateObject = new Date(date + 'T00:00:00');

    return dateObject.toLocaleDateString('pt-BR',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }
    );
}


function getProgress(goal) {

    const target = Number(goal.target_amount) || 0;

    const current = Number(goal.current_amount) || 0;

    if (target <= 0) {
        return 0;
    }
    return Math.min(
        (current / target) * 100,
        100
    );
}



/* =========================================================
   MODAL DE NOVA META
========================================================= */

newGoalButton.addEventListener('click', () => {
    editingGoalId = null;

    document.getElementById('goal-modal-title').textContent = 'Nova meta';
    createGoalButton.textContent = 'Criar meta';

    document.getElementById('goal-name').value = '';
    document.getElementById('goal-target').value = '';
    document.getElementById('goal-deadline').value = '';

    goalMessage.textContent = '';

    goalModal.classList.add('active');
});

closeGoalModal.addEventListener('click', () => {
    goalModal.classList.remove('active');
});

cancelGoalModal.addEventListener('click', () => {
    goalModal.classList.remove('active');
});

/* =========================================================
   CARREGAR METAS
========================================================= */

async function loadGoals() {
    try {
        const response = await apiFetch('http://api.remmick.com/api/savings',
            {
                method: 'GET',
            }
        );
        const responseData =
            await response.json();

        console.log(
            'Metas recebidas:',
            responseData
        );

        if (!response.ok) {
            console.error(
                responseData.message
            );
            return;
        }

        goals = responseData.data || [];

        renderGoals();
        updateSummaryCards();
        updatePace();
        await renderDeposits();
        await renderEvolution();

    } catch (error) {
        console.error(
            'Erro ao carregar metas:',
            error
        );
    }
}


/* =========================================================
   RENDERIZAR METAS
========================================================= */

function renderGoals() {

    goalsList.innerHTML = '';

    goalCount.textContent = `${goals.length} ${goals.length === 1 ? 'meta' : 'metas'}`;

    if (goals.length === 0) {

        goalsList.innerHTML = `
            <div class="empty-state">
                <strong>Você ainda não possui metas.</strong>
                <span>Crie sua primeira meta para começar a guardar dinheiro.</span>
            </div>
        `;
        return;
    }

    goals.forEach(goal => {
        const progress =
            getProgress(goal);

        const goalElement =
            document.createElement('div');

        goalElement.className =
            'goal-item';

        goalElement.innerHTML = `

            <div class="goal-top">

                <div class="goal-name-area">

                    <div class="goal-icon">
                        ${getGoalIcon(goal.name)}
                    </div>

                    <div>

                        <strong>
                            ${escapeHtml(goal.name)}
                        </strong>

                        <span>
                            ${formatDate(goal.deadline)}
                        </span>

                    </div>

                </div>


                <strong class="goal-percentage">
                    ${progress.toFixed(1).replace('.', ',')}%
                </strong>

            </div>


            <div class="progress-bar">

                <span
                    class="progress-fill"
                    style="width: ${progress}%"
                ></span>

            </div>


            <div class="goal-bottom">

                <span>
                    ${formatCurrency(goal.current_amount)}
                    guardados
                </span>

                <strong>
                    ${formatCurrency(goal.target_amount)}
                </strong>

            </div>


            <div class="goal-actions">

                <button
                    class="primary-button add-money-button"
                    data-goal-id="${goal.id}"
                >
                    + Adicionar dinheiro
                </button>

                <button class="secondary-button" onclick="editGoal(${goal.id})">
                    Editar
                </button>

                <button class="secondary-button" onclick="deleteGoal(${goal.id})">
                    Excluir
                </button>

            </div>

        `;
        goalsList.appendChild(
            goalElement
        );
    });

    document
        .querySelectorAll('.add-money-button')
        .forEach(button => {
            button.addEventListener(
                'click',
                () => {
                    const goalId =
                        Number(
                            button.dataset.goalId
                        );
                    openDepositModal(
                        goalId
                    );

                }
            );

        });

}

/* =========================================================
   ÍCONE DA META
========================================================= */

function getGoalIcon(name) {
    const normalizedName = name.toLowerCase();

    if (
        normalizedName.includes('viagem') ||
        normalizedName.includes('viajar')
    ) {
        return '✈';
    }

    if (
        normalizedName.includes('notebook') ||
        normalizedName.includes('computador') ||
        normalizedName.includes('pc')
    ) {
        return '💻';
    }

    if (
        normalizedName.includes('carro') ||
        normalizedName.includes('moto')
    ) {
        return '🚗';
    }

    if (
        normalizedName.includes('casa') ||
        normalizedName.includes('apartamento')
    ) {
        return '⌂';
    }

    if (
        normalizedName.includes('emergência') ||
        normalizedName.includes('emergencia') ||
        normalizedName.includes('reserva')
    ) {
        return '🛡';
    }

    return '◎';

}

/* =========================================================
   EVITAR HTML INJETADO
========================================================= */

function escapeHtml(value) {

    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

}

/* =========================================================
   RESUMO
========================================================= */

function updateSummaryCards() {

    const totalSaved =
        goals.reduce((total, goal) => {
                return total +
                    Number(
                        goal.current_amount
                    );

            },
            0
        );

    const totalTarget =
        goals.reduce((total, goal) => {
                return total +
                    Number(
                        goal.target_amount
                    );

            },
            0
        );

    let overallProgress = 0;

    if (totalTarget > 0) {
        overallProgress =
            (totalSaved / totalTarget) * 100;

    }

    totalSavedElement.textContent =
        formatCurrency(totalSaved);


    totalTargetElement.textContent =
        formatCurrency(totalTarget);


    overallProgressElement.textContent =
        `${Math.min(
            overallProgress,
            100
        ).toFixed(1).replace('.', ',')}%`;

}

/* =========================================================
   MODAL DE APORTE
========================================================= */

function openDepositModal(goalId) {
    const goal =
        goals.find(
            goal =>
                Number(goal.id) === goalId
        );

    if (!goal) {
        return;
    }

    const amount =
        prompt(
            `Quanto você deseja guardar para "${goal.name}"?`
        );

    if (amount === null) {
        return;
    }

    const value =
        Number(
            amount.replace(',', '.')
        );


    if (!Number.isFinite(value) || value <= 0) {

        alert(
            'Digite um valor válido maior que zero.'
        );

        return;

    }

    addMovement(
        goal,
        value
    );

}


/* =========================================================
   ADICIONAR APORTE
========================================================= */

async function addMovement(goal, value) {
    try {
        const today = new Date();

        const movementDate =
            today.toISOString().split('T')[0];

        const response = await apiFetch(`http://api.remmick.com/api/savings/${goal.id}/movements`,
            {
                method: 'POST',
                headers: {
                    'Content-Type':
                        'application/json'
                },
                body: JSON.stringify({
                    value: value,
                    type: 'deposit',
                    description: 'Aporte',
                    movement_at: movementDate
                })
            }
        );

        const responseData =
            await response.json();

        if (!response.ok) {
            console.error(
                'Erro ao registrar aporte:',
                responseData
            );
            alert(
                responseData.message ||
                'Erro ao registrar aporte.'
            );
            return;
        }

        if (!responseData.success) {
            alert(
                responseData.message ||
                'Não foi possível registrar o aporte.'
            );
            return;
        }
        alert('Aporte realizado com sucesso!');

        await loadGoals();

    } catch (error) {
        console.error(
            'Erro ao registrar aporte:',
            error
        );
        alert(
            'Erro ao conectar com o servidor.'
        );
    }
}


/* =========================================================
   ÚLTIMOS APORTES
========================================================= */
async function getGoalMovements(goalId) {

    try {

        const response = await apiFetch(`http://api.remmick.com/api/savings/${goalId}/movements`,
            {
                method: 'GET',
            }
        );

        const responseData =
            await response.json();

        if (!response.ok) {
            console.error(
                'Erro ao buscar movimentações:',
                responseData.message
            );
            return [];
        }
        return responseData.data || [];

    } catch (error) {
        console.error(
            'Erro ao buscar movimentações:',
            error
        );
        return [];
    }
}

async function renderDeposits() {
    depositsList.innerHTML = '';

    const movementRequests =
        goals.map(goal =>
            getGoalMovements(goal.id)
        );

    const movementsByGoal =
        await Promise.all(movementRequests);

    const movements =
        movementsByGoal.flat();

    const deposits =
        movements
            .filter(
                movement =>
                    movement.type === 'deposit'
            )
            .sort(
                (a, b) =>
                    new Date(b.movement_date) -
                    new Date(a.movement_date)
            )
            .slice(0, 4);

    if (deposits.length === 0) {
        depositsList.innerHTML = `
            <div class="empty-state">
                <strong>Nenhum aporte ainda.</strong>
                <span>Adicione dinheiro a uma das suas metas.</span>
            </div>
        `;
        return;
    }

    deposits.forEach(deposit => {
        const item = document.createElement('div');

        item.className = 'deposit-item';

        const goal = goals.find(
                goal =>
                    Number(goal.id) ===
                    Number(deposit.goal_id)
            );

        const goalName = goal
                ? goal.name
                : 'Meta';

        const date = new Date(
                deposit.movement_date + 'T00:00:00'
            );

        item.innerHTML = `

            <div class="deposit-icon">
                +
            </div>

            <div class="deposit-info">

                <strong>
                    ${escapeHtml(goalName)}
                </strong>

                <span>
                    ${date.toLocaleDateString('pt-BR')}
                </span>

            </div>

            <strong class="deposit-value">
                + ${formatCurrency(deposit.amount)}
            </strong>

        `;

        depositsList.appendChild(item);

    });
}



/* =========================================================
   SEU RITMO
========================================================= */

function updatePace() {
    const totalSaved =
        goals.reduce(
            (total, goal) =>
                total +
                Number(goal.current_amount),
            0
        );

    const totalExpected =
        goals.reduce(
            (total, goal) => {
                return total +
                    calculateExpectedAmount(
                        goal
                    );
            },
            0
        );

    const difference =
        totalSaved -
        totalExpected;

    const monthlyRequired =
        calculateMonthlyRequired();

    expectedAmountElement.textContent =
        formatCurrency(
            totalExpected
        );

    currentPaceAmountElement.textContent =
        formatCurrency(
            totalSaved
        );

    monthlyRequiredElement.textContent =
        `${formatCurrency(
            monthlyRequired
        )} / mês`;

    if (difference >= 0) {
        paceStatusTitle.textContent =
            'Você está no ritmo!';

        paceStatusDescription.textContent =
            'Seu valor guardado está acima do esperado para suas metas.';

        paceHighlightLabel.textContent =
            'Acima do esperado';

        paceHighlightValue.textContent =
            `+ ${formatCurrency(
                difference
            )}`;
    } else {

        paceStatusTitle.textContent =
            'Vamos acelerar um pouco';

        paceStatusDescription.textContent =
            'Seu valor guardado está abaixo do esperado para suas metas.';

        paceHighlightLabel.textContent =
            'Abaixo do esperado';

        paceHighlightValue.textContent =
            formatCurrency(
                Math.abs(difference)
            );
    }
}

/* =========================================================
   VALOR ESPERADO
========================================================= */

function calculateExpectedAmount(goal) {
    if (!goal.deadline) {
        return 0;
    }

    const target = Number(goal.target_amount);

    const created = new Date(goal.created_at);

    const deadline = new Date(goal.deadline + 'T23:59:59');

    const today = new Date();

    const totalTime = deadline - created;

    const elapsedTime = today - created;

    if (totalTime <= 0 || elapsedTime <= 0) {
        return 0;
    }

    const percentage = Math.min(elapsedTime / totalTime, 1);

    return target * percentage;
}

/* =========================================================
   VALOR MENSAL NECESSÁRIO
========================================================= */

function calculateMonthlyRequired() {
    let totalRequired = 0;

    const today = new Date();

    goals.forEach(goal => {
        if (!goal.deadline) {
            return;
        }

        const current = Number(goal.current_amount);

        const target = Number(goal.target_amount);

        const remaining = Math.max(target - current, 0);

        const deadline = new Date(goal.deadline + 'T23:59:59');

        const milliseconds = deadline - today;

        const days = milliseconds /
            (
                1000 *
                60 *
                60 *
                24
            );

        const months = Math.max(days / 30, 1);

        totalRequired += remaining / months;
    });
    return totalRequired;
}

/* =========================================================
   EVOLUÇÃO
========================================================= */

function getNiceChartMax(value) {

    if (value <= 1000) return 1000;
    if (value <= 2500) return 2500;
    if (value <= 5000) return 5000;
    if (value <= 10000) return 10000;
    if (value <= 25000) return 25000;
    if (value <= 50000) return 50000;

    return Math.ceil(value / 10000) * 10000;
}


async function renderEvolution() {
    const movementRequests =
        goals.map(goal =>
            getGoalMovements(goal.id)
        );

    const movementsByGoal =
        await Promise.all(
            movementRequests
        );

    const movements =
        movementsByGoal
            .flat()
            .filter(
                movement =>
                    movement.type === 'deposit'
            )
            .sort(
                (a, b) =>
                    new Date(a.movement_date) -
                    new Date(b.movement_date)
            );

    lineChart.innerHTML = '';
    chartLabels.innerHTML = '';
    chartYAxis.innerHTML = '';

    const totalSaved =
        goals.reduce(
            (total, goal) =>
                total +
                Number(
                    goal.current_amount
                ),
            0
        );

    if (movements.length === 0) {
        renderEmptyEvolution(
            totalSaved
        );
        return;
    }

    let accumulated = 0;

    const history =
        movements.map(
            movement => {

                accumulated +=
                    Number(
                        movement.amount
                    );

                return {
                    date:
                        new Date(
                            movement.movement_date +
                            'T00:00:00'
                        ),

                    value:
                        accumulated
                };
            }
        );

    /*
     * Valor máximo real que existe
     * nos dados do gráfico.
     */
    const realMaxValue =
        Math.max(
            totalSaved,
            ...history.map(
                item =>
                    item.value
            ),
            100
        );

    /*
     * Valor máximo "bonito" usado
     * no eixo Y.
     */
    const chartMaxValue =
        getNiceChartMax(
            realMaxValue
        );

    const svg =
        document.createElementNS(
            'http://www.w3.org/2000/svg',
            'svg'
        );

    svg.setAttribute(
        'viewBox',
        '0 0 100 100'
    );

    svg.setAttribute(
        'preserveAspectRatio',
        'none'
    );

    const line =
        document.createElementNS(
            'http://www.w3.org/2000/svg',
            'polyline'
        );

    line.classList.add(
        'evolution-line'
    );

    const points = [];

    history.forEach(
        (item, index) => {

            let horizontalPosition;

            if (history.length === 1) {

                horizontalPosition = 50;

            } else {

                horizontalPosition =
                    (
                        index /
                        (history.length - 1)
                    ) * 100;
            }

            /*
             * Agora usamos chartMaxValue
             * em vez do valor máximo real.
             */
            const verticalPosition =
                100 -
                (
                    item.value /
                    chartMaxValue
                ) * 100;

            points.push(
                `${horizontalPosition},${verticalPosition}`
            );

            const point =
                document.createElement(
                    'div'
                );

            point.className =
                'chart-point';

            point.style.left =
                `${horizontalPosition}%`;

            point.style.bottom =
                `${100 - verticalPosition}%`;

            point.title =
                `${formatCurrency(
                    item.value
                )} - ${
                    item.date.toLocaleDateString(
                        'pt-BR'
                    )
                }`;

            lineChart.appendChild(
                point
            );

            const label =
                document.createElement(
                    'span'
                );

            label.textContent =
                item.date.toLocaleDateString(
                    'pt-BR',
                    {
                        day: '2-digit',
                        month: 'short'
                    }
                );

            chartLabels.appendChild(
                label
            );
        }
    );

    line.setAttribute(
        'points',
        points.join(' ')
    );

    svg.appendChild(
        line
    );

    lineChart.prepend(
        svg
    );

    renderYAxis(
        chartMaxValue
    );
}


function renderEmptyEvolution(totalSaved) {

    const chartMaxValue =
        getNiceChartMax(
            Math.max(
                totalSaved,
                100
            )
        );

    const point =
        document.createElement(
            'div'
        );

    point.className =
        'chart-point';

    point.style.left =
        '50%';

    point.style.bottom =
        totalSaved > 0
            ? `${(
                totalSaved /
                chartMaxValue
            ) * 100}%`
            : '0%';

    point.title =
        formatCurrency(
            totalSaved
        );

    lineChart.appendChild(
        point
    );

    const label =
        document.createElement(
            'span'
        );

    label.textContent =
        'Agora';

    chartLabels.appendChild(
        label
    );

    renderYAxis(
        chartMaxValue
    );
}



/* =========================================================
   EIXO Y
========================================================= */

function renderYAxis(maxValue) {

    chartYAxis.innerHTML = '';


    for (
        let i = 4;
        i >= 0;
        i--
    ) {

        const value =
            (
                maxValue / 4
            ) * i;


        const span =
            document.createElement('span');


        span.textContent =
            formatCurrency(
                value
            );


        chartYAxis.appendChild(
            span
        );

    }

}



/* =========================================================
   SIMULADOR
========================================================= */

function updateSimulator() {

    const monthly =
        Number(
            monthlySavingInput.value
        ) || 0;


    const months =
        Number(
            savingMonthsSelect.value
        ) || 0;


    const result =
        monthly * months;


    simulatorResult.textContent =
        formatCurrency(
            result
        );

}


monthlySavingInput.addEventListener(
    'input',
    updateSimulator
);


savingMonthsSelect.addEventListener(
    'change',
    updateSimulator
);



/* =========================================================
   CRIAR META
========================================================= */

createGoalButton.addEventListener('click', async (e) => {
    e.preventDefault();

    const goalName = document.getElementById('goal-name').value.trim();
    const goalTarget = document.getElementById('goal-target').value;
    const goalDeadline = document.getElementById('goal-deadline').value;

    if (!goalTarget) {
        goalMessage.style.color = '#ef4444';
        goalMessage.textContent = 'Adicione um valor';
        return;
    }

    if (!goalName) {
        goalMessage.style.color = '#ef4444';
        goalMessage.textContent = 'Adicione um nome a sua meta';
        return;
    }

    const goal = {
        name: goalName,
        target_amount: goalTarget,
        deadline: goalDeadline
    };

    try {
        let response;

        if (editingGoalId !== null) {

            response = await apiFetch(
                `http://api.remmick.com/api/savings/${editingGoalId}`,
                {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(goal)
                }
            );

        } else {

            response = await apiFetch(
                'http://api.remmick.com/api/savings',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(goal)
                }
            );
        }

        const responseData = await response.json();

        if (!response.ok) {
            goalMessage.style.color = '#ef4444';
            goalMessage.textContent =
                responseData.message || 'Erro ao salvar meta.';
            return;
        }

        if (responseData.success) {

            goalMessage.style.color = '#10b981';

            if (editingGoalId !== null) {
                goalMessage.textContent = '✅ Meta alterada com sucesso!';
            } else {
                goalMessage.textContent = '✅ Meta criada com sucesso!';
            }

            await loadGoals();

            document.getElementById('goal-name').value = '';
            document.getElementById('goal-target').value = '';
            document.getElementById('goal-deadline').value = '';

            editingGoalId = null;

            document.getElementById('goal-modal-title').textContent = 'Nova meta';
            createGoalButton.textContent = 'Criar meta';

            setTimeout(() => {
                goalModal.classList.remove('active');
                goalMessage.textContent = '';
            }, 800);
        }

    } catch (error) {
        console.error('Erro ao salvar meta:', error);

        goalMessage.style.color = '#ef4444';
        goalMessage.textContent =
            'Erro ao conectar com o servidor.';
    };

});


    function editGoal(goalId) {
        const goal = goals.find(
            goal => Number(goal.id) === Number(goalId)
        );

        if (!goal) {
            alert('Meta não encontrada.');
            return;
        }

        editingGoalId = goal.id;

        document.getElementById('goal-name').value = goal.name;
        document.getElementById('goal-target').value = goal.target_amount;
        document.getElementById('goal-deadline').value = goal.deadline || '';

        document.getElementById('goal-modal-title').textContent = 'Editar meta';
        createGoalButton.textContent = 'Salvar alterações';

        goalMessage.textContent = '';

        goalModal.classList.add('active');
    }

    async function deleteGoal(goalId) {
        const goal = goals.find(
            goal => Number(goal.id) === Number(goalId)
        );

        if (!goal) {
            alert('Meta não encontrada.');
            return;
        }

        const confirmed = confirm(
            `Deseja realmente excluir a meta "${goal.name}"?`
        );

        if (!confirmed) return;

        try {
            const response = await apiFetch(
                `http://api.remmick.com/api/savings/${goalId}`,
                {
                    method: 'DELETE',
                }
            );

            const responseData = await response.json();

            if (!response.ok) {
                alert(responseData.message || 'Erro ao excluir meta.');
                return;
            }

            goals = goals.filter(
                goal => Number(goal.id) !== Number(goalId)
            );

            renderGoals();
            updateSummaryCards();

            alert('Meta excluída com sucesso!');

        } catch (error) {
            console.error('Erro ao excluir meta:', error);
            alert('Erro ao conectar com o servidor.');
        }
    }

        window.editGoal = editGoal;
        window.deleteGoal = deleteGoal;

document.addEventListener(
    'DOMContentLoaded',
    () => {

        loadGoals();

        updateSimulator();

    }
);