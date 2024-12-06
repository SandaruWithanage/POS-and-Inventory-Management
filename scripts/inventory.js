document.addEventListener("DOMContentLoaded", function () {
    const itemsPerPage = 5; // Pagination: 5 inventory items per page
    let currentPage = 1;
    let inventory = [];

    // Load inventory data from inventory.json
    function loadInventory() {
        fetch('data/inventory.json') // Make sure inventory.json is in the correct path (e.g., /data/inventory.json)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch inventory data');
                }
                return response.json();
            })
            .then(data => {
                inventory = data.inventory; // Assumes your JSON structure has "inventory" array
                displayInventory(); // Call the function to display the data
                updatePagination(); // Update pagination once data is loaded
            })
            .catch(error => console.error('Error loading inventory:', error));
    }

    // Display the current page of inventory data
    function displayInventory() {
        const inventoryTableBody = document.querySelector('#inventoryTable tbody');
        inventoryTableBody.innerHTML = '';

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const itemsToDisplay = inventory.slice(startIndex, endIndex);

        itemsToDisplay.forEach(item => {
            const totalValue = item.quantity * item.price; // Calculate total value
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>${item.id}</td>
                <td>${item.name}</td>
                <td>${item.barcode}</td>
                <td>${item.category}</td>
                <td>${item.quantity}</td>
                <td>${item.price.toFixed(2)}</td> <!-- Format the price to 2 decimal places -->
                <td>${totalValue.toFixed(2)}</td> <!-- Format total value to 2 decimal places -->
                <td>
                    <button class="editBtn" data-id="${item.id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="deleteBtn" data-id="${item.id}">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </td>
            `;

            inventoryTableBody.appendChild(row);
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

    // Edit functionality
    function handleEdit(event) {
        const itemId = event.target.closest('button').getAttribute('data-id');
        alert(`Edit item with ID: ${itemId}`);
        // Implement the actual edit functionality here
    }

    // Delete functionality
    function handleDelete(event) {
        const itemId = event.target.closest('button').getAttribute('data-id');
        const confirmed = confirm(`Are you sure you want to delete item with ID: ${itemId}?`);
        if (confirmed) {
            inventory = inventory.filter(item => item.id !== parseInt(itemId));
            displayInventory(); // Update the table after deletion
            updatePagination(); // Update pagination after deletion
        }
    }

    // Update pagination controls
    function updatePagination() {
        const totalPages = Math.ceil(inventory.length / itemsPerPage);
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');
        const currentPageLabel = document.getElementById('currentPage');

        prevButton.disabled = currentPage === 1;
        nextButton.disabled = currentPage === totalPages;

        currentPageLabel.textContent = `Page ${currentPage} of ${totalPages}`;
    }

    // Pagination controls
    document.getElementById('prevPage').addEventListener('click', function () {
        if (currentPage > 1) {
            currentPage--;
            displayInventory();
            updatePagination();
        }
    });

    document.getElementById('nextPage').addEventListener('click', function () {
        const totalPages = Math.ceil(inventory.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            displayInventory();
            updatePagination();
        }
    });

    loadInventory(); // Load the inventory data on page load
});
