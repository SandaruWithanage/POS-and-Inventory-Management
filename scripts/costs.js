// DOM Elements
const costTable = document.getElementById("costTable").querySelector("tbody");
const costModal = document.getElementById("costModal");
const modalTitle = document.getElementById("modalTitle");
const costForm = document.getElementById("costForm");
const closeModalBtn = document.querySelector(".close-btn");

// State
let editMode = false;
let editRow = null;

// Event Listeners
document.getElementById("addCostBtn").addEventListener("click", () => {
  editMode = false;
  costForm.reset();
  modalTitle.textContent = "Add New Cost";
  costModal.style.display = "block";
});

costTable.addEventListener("click", (event) => {
  const target = event.target;
  const row = target.closest("tr");

  if (target.classList.contains("edit-btn")) {
    editMode = true;
    editRow = row;

    const costId = row.cells[0].textContent;
    const costRate = row.cells[1].textContent.replace("%", "");
    const amount = row.cells[2].textContent.replace(/,/g, "");

    document.getElementById("costId").value = costId;
    document.getElementById("costRate").value = costRate;
    document.getElementById("amount").value = amount;

    modalTitle.textContent = "Edit Cost";
    costModal.style.display = "block";
  } else if (target.classList.contains("delete-btn")) {
    if (confirm("Are you sure you want to delete this cost?")) {
      row.remove();
    }
  }
});

costForm.addEventListener("submit", (event) => {
  event.preventDefault();

  const costId = document.getElementById("costId").value || `COST${Date.now()}`;
  const costRate = document.getElementById("costRate").value;
  const amount = parseFloat(document.getElementById("amount").value).toLocaleString();

  if (editMode) {
    // Update existing row
    editRow.cells[1].textContent = `${costRate}%`;
    editRow.cells[2].textContent = amount;
  } else {
    // Add new row
    const newRow = costTable.insertRow();
    newRow.innerHTML = `
      <td>${costId}</td>
      <td>${costRate}%</td>
      <td>${amount}</td>
      <td>
        <button class="edit-btn">Edit</button>
        <button class="delete-btn">Delete</button>
      </td>
    `;
  }

  costModal.style.display = "none";
  costForm.reset();
});

closeModalBtn.addEventListener("click", () => {
  costModal.style.display = "none";
});

// Close modal on outside click
window.addEventListener("click", (event) => {
  if (event.target === costModal) {
    costModal.style.display = "none";
  }
});
