document.addEventListener('DOMContentLoaded', () => {
    const costTable = document.querySelector('#costTable tbody');
    const addCostBtn = document.getElementById('addCostBtn');
  
    let lastCostId = 2; // Tracks the last generated Cost ID (starts from existing entries)
  
    // Inline Editing for Costs Table
    costTable.addEventListener('click', (e) => {
      if (e.target.classList.contains('edit-btn')) {
        const row = e.target.closest('tr');
        row.contentEditable = true; // Makes row editable
        e.target.textContent = "Save"; // Change button to "Save"
        e.target.classList.add('save-btn'); // Add save-btn class
        e.target.classList.remove('edit-btn'); // Remove edit-btn class
      } else if (e.target.classList.contains('save-btn')) {
        const row = e.target.closest('tr');
        row.contentEditable = false; // Stops editing
        e.target.textContent = "Edit"; // Change button back to "Edit"
        e.target.classList.add('edit-btn'); // Add edit-btn class
        e.target.classList.remove('save-btn'); // Remove save-btn class
      }
    });
  
    // Handle Delete Button
    costTable.addEventListener('click', (e) => {
      if (e.target.classList.contains('delete-btn')) {
        const row = e.target.closest('tr');
        row.remove();
      }
    });
  
    // Redirect to Add Cost Form
    addCostBtn.addEventListener('click', () => {
      window.location.href = "costForm.html";
    });
  });
  