document.addEventListener("DOMContentLoaded", function () {
    const costsPerPage = 5;
    let currentPage = 1;
    let costs = [];
  
    // Fetch and load costs
    function loadCosts() {
      fetch('data/cost.json')
        .then(response => response.json())
        .then(data => {
          costs = data.costs;
          displayCosts();
          updatePagination();
        })
        .catch(error => console.error('Error loading costs:', error));
    }
  
    // Display costs in the table
    function displayCosts() {
      const costTableBody = document.querySelector('#costTable tbody');
      costTableBody.innerHTML = '';
  
      const startIndex = (currentPage - 1) * costsPerPage;
      const endIndex = startIndex + costsPerPage;
      const costsToDisplay = costs.slice(startIndex, endIndex);
  
      costsToDisplay.forEach(cost => {
        const row = document.createElement('tr');
  
        row.innerHTML = `
          <td>${cost.id}</td>
          <td>${cost.category}</td>
          <td>${cost.description}</td>
          <td>${cost.amount}</td>
          <td>${cost.date}</td>
          <td>
            <button class="editBtn" data-id="${cost.id}">
              <i class="fas fa-edit"></i> Edit
            </button>
            <button class="deleteBtn" data-id="${cost.id}">
              <i class="fas fa-trash-alt"></i> Delete
            </button>
          </td>
        `;
  
        costTableBody.appendChild(row);
      });
  
      // Add event listeners to Edit and Delete buttons
      const editButtons = document.querySelectorAll('.editBtn');
      const deleteButtons = document.querySelectorAll('.deleteBtn');
  
      editButtons.forEach(button => {
        button.addEventListener('click', handleEdit);
      });
  
      deleteButtons.forEach(button => {
        button.addEventListener('click', handleDelete);
      });
    }
  
    // Handle Edit button click
    function handleEdit(event) {
      const costId = event.target.closest('button').getAttribute('data-id');
      alert(`Edit cost with ID: ${costId}`);
      // Implement edit functionality as needed
    }
  
    // Handle Delete button click
    function handleDelete(event) {
      const costId = event.target.closest('button').getAttribute('data-id');
      const confirmed = confirm(`Are you sure you want to delete this cost entry with ID: ${costId}?`);
      if (confirmed) {
        costs = costs.filter(cost => cost.id !== parseInt(costId));
        saveCosts(); // Save the updated costs back to the JSON file
        displayCosts();
        updatePagination();
      }
    }
  
    // Update pagination
    function updatePagination() {
      const totalPages = Math.ceil(costs.length / costsPerPage);
      const prevButton = document.getElementById('prevPage');
      const nextButton = document.getElementById('nextPage');
      const currentPageLabel = document.getElementById('currentPage');
  
      prevButton.disabled = currentPage === 1;
      nextButton.disabled = currentPage === totalPages;
  
      currentPageLabel.textContent = `Page ${currentPage} of ${totalPages}`;
    }
  
    // Handle Previous Page
    document.getElementById('prevPage').addEventListener('click', function () {
      if (currentPage > 1) {
        currentPage--;
        displayCosts();
        updatePagination();
      }
    });
  
    // Handle Next Page
    document.getElementById('nextPage').addEventListener('click', function () {
      const totalPages = Math.ceil(costs.length / costsPerPage);
      if (currentPage < totalPages) {
        currentPage++;
        displayCosts();
        updatePagination();
      }
    });
  
    // Simulate saving costs back to the server
    function saveCosts() {
      fetch('/save_costs_endpoint', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ costs })
      })
        .then(response => response.json())
        .then(data => console.log('Costs saved successfully.', data))
        .catch(error => console.error('Error saving costs:', error));
    }
  
    loadCosts();
  });
  