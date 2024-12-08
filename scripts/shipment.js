document.addEventListener("DOMContentLoaded", function() {
    const shipmentsPerPage = 5;
    let currentPage = 1;
    let shipments = [];

    function loadShipments() {
        fetch('data/shipment.json')
            .then(response => response.json())
            .then(data => {
                shipments = data.shipments;
                displayShipments();
                updatePagination();
            })
            .catch(error => console.error('Error loading shipments:', error));
    }

    function displayShipments() {
        const shipmentTableBody = document.querySelector('#shipmentTable tbody');
        shipmentTableBody.innerHTML = '';

        const startIndex = (currentPage - 1) * shipmentsPerPage;
        const endIndex = startIndex + shipmentsPerPage;
        const shipmentsToDisplay = shipments.slice(startIndex, endIndex);

        shipmentsToDisplay.forEach(shipment => {
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>${shipment.id}</td>
                <td>${shipment.quantity}</td>
                <td>${shipment.date}</td>
                <td>
                    <button class="editBtn" data-id="${shipment.id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="deleteBtn" data-id="${shipment.id}">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </td>
            `;

            shipmentTableBody.appendChild(row);
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
        const shipmentId = event.target.closest('button').getAttribute('data-id');
        alert(`Edit shipment with ID: ${shipmentId}`);
        // Implement the actual edit functionality here
    }

    function handleDelete(event) {
        const shipmentId = event.target.closest('button').getAttribute('data-id');
        const confirmed = confirm(`Are you sure you want to delete shipment with ID: ${shipmentId}?`);
        if (confirmed) {
            // Implement the actual delete functionality here
            shipments = shipments.filter(shipment => shipment.id !== parseInt(shipmentId));
            displayShipments(); // Update the table after deletion
        }
    }

    function updatePagination() {
        const totalPages = Math.ceil(shipments.length / shipmentsPerPage);
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
            displayShipments();
            updatePagination();
        }
    });

    document.getElementById('nextPage').addEventListener('click', function() {
        const totalPages = Math.ceil(shipments.length / shipmentsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            displayShipments();
            updatePagination();
        }
    });

    loadShipments();
});
