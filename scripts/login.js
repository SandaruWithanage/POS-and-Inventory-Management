document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    // User data with roles (no dashboard URLs)
    const users = [
        { username: "supply_manager", password: "supply123", role: "Supply Manager" },
        { username: "admin", password: "admin123", role: "Admin" },
        { username: "customer_manager", password: "customer123", role: "Customer Relation Manager" },
        { username: "finance_manager", password: "finance123", role: "Finance Manager" },
        { username: "procurement_manager", password: "procurement123", role: "Procurement Manager" },
        { username: "cashier", password: "cashier123", role: "Cashier" }
    ];

    // Find the user based on the provided username and password
    const user = users.find(user => user.username === username && user.password === password);

    if (user) {
        // Redirect based on the user's role
        switch (user.role) {
            case "Supply Manager":
                window.location.href = "SupplyManager/supply_manager_dashboard.html";
                break;
            case "Admin":
                window.location.href = "dashboard.html";
                break;
            case "Customer Relation Manager":
                window.location.href = "CustomerRelation/customer_manager_dashboard.html";
                break;
            case "Finance Manager":
                window.location.href = "FinanceManager/finance_manager_dashboard.html";
                break;
            case "Procurement Manager":
                window.location.href = "ProcumentManager/procurement_manager_dashboard.html";
                break;
            case "Cashier":
                window.location.href = "Cashier/CashierDashboard.html";
                break;
            default:
                document.getElementById('error-message').textContent = "No dashboard available for this role";
        }
    } else {
        // Show error message for invalid credentials
        document.getElementById('error-message').textContent = "Invalid username or password";
    }
});
