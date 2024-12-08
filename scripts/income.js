document.addEventListener("DOMContentLoaded", function() {
    const incomesPerPage = 5;
    let currentPage = 1;
    let incomes = [];

    function loadIncomes() {
        fetch('data/income.json')
            .then(response => response.json())
            .then(data => {
                incomes = data.incomes;
                displayIncomes();
                updatePagination();
            })
            .catch(error => console.error('Error loading incomes:', error));
    }

    function displayIncomes() {
        const incomeTableBody = document.querySelector('#incomeTable tbody');
        incomeTableBody.innerHTML = '';

        const startIndex = (currentPage - 1) * incomesPerPage;
        const endIndex = startIndex + incomesPerPage;
        const incomesToDisplay = incomes.slice(startIndex, endIndex);

        incomesToDisplay.forEach(income => {
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>${income.id}</td>
                <td>${income.rate}</td>
                <td>${income.amount}</td>
                <td>
                    <button class="editBtn" data-id="${income.id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="deleteBtn" data-id="${income.id}">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </td>
            `;

            incomeTableBody.appendChild(row);
        });

        // Add event listeners for Edit and Delete buttons
        const editButtons = document.querySelectorAll('.editBtn');
        const deleteButtons = document.querySelectorAll('.deleteBtn');

        editButtons.forEach(button => {
            button.addEventListener('click', handleEdit);
        });

        deleteButtons.forEach(button => {
            button.addEventListener('click', handleDelete);
        });
    }

    function handleEdit(event) {
        const incomeId = event.target.closest('button').getAttribute('data-id');
        alert(`Edit income with ID: ${incomeId}`);
        // Implement the actual edit functionality here
    }

    function handleDelete(event) {
        const incomeId = event.target.closest('button').getAttribute('data-id');
        const confirmed = confirm(`Are you sure you want to delete income with ID: ${incomeId}?`);
        if (confirmed) {
            // Implement the actual delete functionality here
            incomes = incomes.filter(income => income.id !== parseInt(incomeId));
            displayIncomes(); // Update the table after deletion
        }
    }

    function updatePagination() {
        const totalPages = Math.ceil(incomes.length / incomesPerPage);
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');
        const currentPageLabel = document.getElementById('currentPage');

        prevButton.disabled = currentPage === 1;
        nextButton.disabled = currentPage === totalPages;

        currentPageLabel.textContent = `Page ${currentPage} of ${totalPages}`;
    }

    document.getElementById('prevPage').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            displayIncomes();
            updatePagination();
        }
    });

    document.getElementById('nextPage').addEventListener('click', function() {
        const totalPages = Math.ceil(incomes.length / incomesPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            displayIncomes();
            updatePagination();
        }
    });

    loadIncomes();
});