const newGoalButton = document.getElementById('new-goal-button');
const goalModal = document.getElementById('goal-modal');
const closeGoalModal = document.getElementById('close-goal-modal');
const cancelGoalModal = document.getElementById('cancel-goal-modal');
const createGoalButton = document.getElementById('create-goal-button');
const goalMessage = document.getElementById('goal-message');
const totalSavedElement = document.getElementById('total-saved');
const totalTargetElement = document.getElementById('total-target');
const overallProgressElement = document.getElementById('overall-progress');


newGoalButton.addEventListener('click', () => {
    goalModal.classList.add('active');
});

closeGoalModal.addEventListener('click', () => {
    goalModal.classList.remove('active');
});

cancelGoalModal.addEventListener('click', () => {
    goalModal.classList.remove('active');
});


async function loadGoals() {
    const response = await fetch('http://api.remmick.com/api/savings', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }
    );

    const responseData = await response.json();

    console.log('Metas recebidas:', responseData);

    if (!response.ok) {
        console.error(responseData.message);
        return;
    }

    const goals = responseData.data;

    console.log('Lista de metas:', goals);

    updateSummaryCards(goals);
}

function updateSummaryCards(goals) {

    const totalSaved = goals.reduce((total, goal) => {
        return total + Number(goal.current_amount);
    }, 0);


    const totalTarget = goals.reduce((total, goal) => {
        return total + Number(goal.target_amount);
    }, 0);


    let overallProgress = 0;

    if (totalTarget > 0) {
        overallProgress =
            (totalSaved / totalTarget) * 100;
    }


    const currencyFormatter = new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });


    totalSavedElement.textContent =
        currencyFormatter.format(totalSaved);


    totalTargetElement.textContent =
        currencyFormatter.format(totalTarget);


    overallProgressElement.textContent =
        `${overallProgress.toFixed(1).replace('.', ',')}%`;
}

createGoalButton.addEventListener('click', async (e) => {
    e.preventDefault();

    const goalName =
        document.getElementById('goal-name').value;

    const goalTarget =
        document.getElementById('goal-target').value;

    const goalDeadline =
        document.getElementById('goal-deadline').value;


    const goal = {
        name: goalName,
        target_amount: goalTarget,
        deadline: goalDeadline
    };

    if (!goalTarget) {
        goalMessage.style.color = '#ef4444';
        goalMessage.textContent = 'Adicione um valor';
        return;
    }


    if (!goalName.trim()) {
        goalMessage.style.color = '#ef4444';
        goalMessage.textContent = 'Adicione um nome a sua meta';
        return;
    }

    try {
        const response = await fetch('http://api.remmick.com/api/savings', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            },

            body: JSON.stringify(goal)

            }

        );


        const responseData =
            await response.json();

        if (!response.ok) {
            goalMessage.style.color = '#ef4444';
            goalMessage.textContent = responseData.message;
            return;
        }

        if (responseData.success) {
            goalMessage.style.color = '#10b981';
            goalMessage.textContent = '✅ Meta criada com sucesso!';

            await loadGoals();

            document.getElementById('goal-name').value = '';
            document.getElementById('goal-target').value = '';
            document.getElementById('goal-deadline').value = '';
        }

    } catch (error) {
        console.error('Erro ao criar meta:', error);
        goalMessage.style.color = '#ef4444';
        goalMessage.textContent ='Erro ao conectar com o servidor.';
    }

});

document.addEventListener(
    'DOMContentLoaded',
    loadGoals
);