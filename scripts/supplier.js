document.addEventListener("DOMContentLoaded", function() {
    const suppliersPerPage = 5;
    let currentPage = 1;
    let suppliers = [];

    function loadSuppliers() {
        fetch('data/supplier.json')
            .then(response => response.json())
            .then(data => {
                suppliers = data.suppliers;
                displaySuppliers();
                updatePagination();
            })
            .catch(error => console.error('Error loading suppliers:', error));
    }

    function displaySuppliers() {
        const supplierTableBody = document.querySelector('#supplierTable tbody');
        supplierTableBody.innerHTML = '';

        const startIndex = (currentPage - 1) * suppliersPerPage;
        const endIndex = startIndex + suppliersPerPage;
        const suppliersToDisplay = suppliers.slice(startIndex, endIndex);

        suppliersToDisplay.forEach(supplier => {
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>${supplier.id}</td>
                <td>${supplier.name}</td>
                <td>${supplier.email}</td>
                <td>${supplier.phone}</td>
                <td>${supplier.address}</td>
                <td>
                    <button class="editBtn" data-id="${supplier.id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="deleteBtn" data-id="${supplier.id}">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </td>
            `;

            supplierTableBody.appendChild(row);
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
        const supplierId = event.target.closest('button').getAttribute('data-id');
        alert(`Edit supplier with ID: ${supplierId}`);
        // Implement the actual edit functionality here
    }

    function handleDelete(event) {
        const supplierId = event.target.closest('button').getAttribute('data-id');
        const confirmed = confirm(`Are you sure you want to delete supplier with ID: ${supplierId}?`);
        if (confirmed) {
            // Implement the actual delete functionality here
            suppliers = suppliers.filter(supplier => supplier.id !== parseInt(supplierId));
            displaySuppliers(); // Update the table after deletion
        }
    }

    function updatePagination() {
        const totalPages = Math.ceil(suppliers.length / suppliersPerPage);
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
            displaySuppliers();
            updatePagination();
        }
    });

    document.getElementById('nextPage').addEventListener('click', function() {
        const totalPages = Math.ceil(suppliers.length / suppliersPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            displaySuppliers();
            updatePagination();
        }
    });

    loadSuppliers();
});
