// Sample pipeline data
let pipelineData = {
    new: [],
    qualified: [
        {
            id: 1,
            name: "Website Redesign Project",
            company: "Acme Corp",
            value: 25000,
            email: "contact@acmecorp.com",
            phone: "+1 234-567-8900",
            assignees: [
                { img: "https://via.placeholder.com/32", name: "John Doe" },
                { img: "https://via.placeholder.com/32", name: "Jane Smith" }
            ],
            lastActivity: "2024-03-10",
            color: "#dc3545"
        }
    ],
    proposition: [
        {
            id: 2,
            name: "Mobile App Development",
            company: "Tech Solutions Inc",
            value: 45000,
            email: "info@techsolutions.com",
            phone: "+1 234-567-8901",
            assignees: [
                { img: "https://via.placeholder.com/32", name: "Mike Johnson" }
            ],
            lastActivity: "2024-03-12",
            color: "#28a745"
        }
    ],
    won: []
};

let currentEditingId = null;
let selectedColor = null;

// Load data from localStorage if exists
function loadPipelineData() {
    const savedData = localStorage.getItem('pipelineData');
    if (savedData) {
        pipelineData = JSON.parse(savedData);
    }
}

// Save data to localStorage
function savePipelineData() {
    localStorage.setItem('pipelineData', JSON.stringify(pipelineData));
}

document.addEventListener('DOMContentLoaded', function () {
    loadPipelineData();
    initializePipeline();
    initializeColorPickers();
    initializeDragAndDrop();
});

function initializePipeline() {
    Object.keys(pipelineData).forEach(stage => {
        renderStageCards(stage);
        updateStageCount(stage);
    });
}

function initializeColorPickers() {
    const colorOptions = document.querySelectorAll('#colorPicker .color-option');
    colorOptions.forEach(option => {
        option.addEventListener('click', () => {
            colorOptions.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
            selectedColor = option.dataset.color;
        });
    });

    const editColorPicker = document.getElementById('editColorPicker');
    editColorPicker.innerHTML = document.getElementById('colorPicker').innerHTML;

    const editColorOptions = editColorPicker.querySelectorAll('.color-option');
    editColorOptions.forEach(option => {
        option.addEventListener('click', () => {
            editColorOptions.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
            updateCardColor(currentEditingId, option.dataset.color);
        });
    });
}

function renderStageCards(stage) {
    const stageElement = document.querySelector(`.pipeline-stage[data-stage="${stage}"]`);
    if (!stageElement) return;

    const cardsContainer = stageElement.querySelector('.pipeline-cards');
    cardsContainer.innerHTML = ''; // Clear existing cards

    pipelineData[stage].forEach(opportunity => {
        const card = createOpportunityCard(opportunity);
        cardsContainer.appendChild(card);
    });
}

function createOpportunityCard(opportunity) {
    const card = document.createElement('div');
    card.className = 'pipeline-card';
    card.draggable = true;
    card.dataset.id = opportunity.id;

    card.innerHTML = `
        <div class="card-label" style="background-color: ${opportunity.color || '#e9ecef'}"></div>
        <div class="pipeline-card-header">
            <div>
                <div class="opportunity-name">${opportunity.name}</div>
                <div class="opportunity-company">${opportunity.company}</div>
            </div>
            <div class="opportunity-value">$${opportunity.value.toLocaleString()}</div>
        </div>
        <div class="pipeline-card-footer">
            <div class="d-flex align-items-center">
                ${opportunity.assignees.map(assignee => `
                    <img src="${assignee.img}" alt="${assignee.name}" title="${assignee.name}" class="avatar">
                `).join('')}
            </div>
            <div class="action-buttons">
                <button class="btn btn-icon" onclick="showComments(${opportunity.id})">
                    <i class="far fa-comment"></i>
                </button>
                <button class="btn btn-icon" onclick="showEditMenu(${opportunity.id})">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>
        <div class="card-actions">
            <button class="btn-edit" onclick="editOpportunity(${opportunity.id})">
                <i class="fas fa-edit"></i> Edit
            </button>
            <button class="btn-delete" onclick="deleteOpportunity(${opportunity.id})">
                <i class="fas fa-trash-alt"></i> Delete
            </button>
        </div>
    `;

    card.addEventListener('dragstart', handleDragStart);
    card.addEventListener('dragend', handleDragEnd);

    return card;
}

function handleDragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.dataset.id);
    e.target.classList.add('dragging');
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
}

function initializeDragAndDrop() {
    const stages = document.querySelectorAll('.pipeline-stage');

    stages.forEach(stage => {
        stage.addEventListener('dragover', handleDragOver);
        stage.addEventListener('drop', handleDrop);
    });
}

function handleDragOver(e) {
    e.preventDefault();
    const afterElement = getDragAfterElement(e.currentTarget, e.clientY);
    const draggable = document.querySelector('.dragging');
    const container = e.currentTarget.querySelector('.pipeline-cards');

    if (afterElement) {
        container.insertBefore(draggable, afterElement);
    } else {
        container.appendChild(draggable);
    }
}

function handleDrop(e) {
    e.preventDefault();
    const cardId = parseInt(e.dataTransfer.getData('text/plain'));
    const newStage = e.currentTarget.dataset.stage;

    moveOpportunity(cardId, newStage);
}

function moveOpportunity(cardId, newStage) {
    let opportunity;
    let oldStage;

    Object.keys(pipelineData).forEach(stage => {
        const index = pipelineData[stage].findIndex(opp => opp.id === cardId);
        if (index !== -1) {
            opportunity = pipelineData[stage].splice(index, 1)[0];
            oldStage = stage;
        }
    });

    if (opportunity) {
        pipelineData[newStage].push(opportunity);
        savePipelineData();
        renderStageCards(oldStage);
        renderStageCards(newStage);
        updateStageCount(oldStage);
        updateStageCount(newStage);
    }
}

function updateStageCount(stage) {
    const stageElement = document.querySelector(`.pipeline-stage[data-stage="${stage}"]`);
    if (!stageElement) return;

    const countElement = stageElement.querySelector('.stage-count');
    if (countElement) {
        countElement.textContent = pipelineData[stage].length;
    }
}

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.pipeline-card:not(.dragging)')];

    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;

        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}