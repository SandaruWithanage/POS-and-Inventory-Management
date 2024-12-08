<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the number of items per page
$itemsPerPage = 10;

// Get current page and search term from the request
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Calculate the offset for the SQL query
$offset = ($page - 1) * $itemsPerPage;

// Fetch inventory data
$sql = "SELECT * FROM products WHERE product_name LIKE ? LIMIT $offset, $itemsPerPage";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total number of records for pagination
$totalSql = "SELECT COUNT(*) as total FROM products WHERE product_name LIKE ?";
$totalStmt = $conn->prepare($totalSql);
$totalStmt->bind_param("s", $searchTerm);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];

// Calculate total pages
$totalPages = ceil($totalRecords / $itemsPerPage);

$inventoryData = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $inventoryData[] = $row;
    }
}

$conn->close();

// Handle if the request is for data fetching (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_inventory'])) {
    echo json_encode([
        'data' => $inventoryData,
        'currentPage' => $page,
        'totalPages' => $totalPages
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="styles/sidebar.css">
    <link rel="stylesheet" href="styles/topbar.css">
    <link rel="stylesheet" href="styles/inventory.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <ul>
                <li><a href="dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
                <li><a href="suppliers.html"><i class="fas fa-truck"></i> Suppliers</a></li>
                <li><a href="budget.html"><i class="fas fa-coins"></i> Budget</a></li>
                <li><a href="costs.html"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
                <li><a href="income-costs.html"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
                <li><a href="sales.html"><i class="fas fa-chart-line"></i> Sales</a></li>
                <li><a href="orders.html"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="customers.html"><i class="fas fa-users"></i> Customer Management</a></li>
                <li><a href="shipment.html"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
                <li><a href="purches.html"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
                <li><a href="roles.html"><i class="fas fa-user-cog"></i> Role Management</a></li>
            </ul>
            <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
        </aside>

        <main class="main-content">
            <header>
                <div class="top-bar">
                    <div class="logo">
                        <img src="assets/logo.jpg" alt="Logo">
                    </div>
                    <div class="search-bar">
                        <input type="text" placeholder="Type for search" id="searchInput">
                    </div>
                    <div class="user-icons">
                        <span class="icon"><i class="fas fa-bell"></i></span>
                        <span class="icon"><i class="fas fa-comments"></i></span>
                        <a href="profile.html">
                            <span class="icon"><i class="fas fa-user-circle"></i></span>
                        </a>
                    </div>
                </div>
            </header>

            <h1>Inventory</h1>

            <div class="table-header">
                <a href="inventoryForm.php">
                    <button id="addInventoryBtn">Add New Inventory Item</button>
                </a>
            </div>

            <table id="inventoryTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Barcode No:</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Selling Price</th> <!-- Added Selling Price column -->
                        <th>Total Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic inventory data will be inserted here -->
                </tbody>
            </table>

            <div class="pagination">
                <button id="prevPage">Previous</button>
                <span id="currentPage">Page 1</span>
                <button id="nextPage">Next</button>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let currentPage = 1;
            const itemsPerPage = 10;

            function fetchInventoryData(page = 1, search = '') {
                fetch(`inventory.php?fetch_inventory=true&page=${page}&search=${search}`)
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = document.querySelector('#inventoryTable tbody');
                        tableBody.innerHTML = ''; // Clear existing table rows
                        
                        data.data.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${item.id}</td>
                                <td>${item.product_name}</td>
                                <td>${item.barcode_no}</td>
                                <td>${item.category}</td>
                                <td>${item.quantity}</td>
                                <td>${item.unit_price}</td>
                                <td>${item.selling_price}</td> <!-- Display Selling Price -->
                                <td>${(item.quantity * item.unit_price).toFixed(2)}</td>
                                <td>
                                    <a href="edit_inventory.php?id=${item.id}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    |
                                    <a href="delete_inventory.php?id=${item.id}">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            `;
                            tableBody.appendChild(row);
                        });

                        document.getElementById('currentPage').textContent = `Page ${data.currentPage}`;
                        document.getElementById('prevPage').disabled = data.currentPage <= 1;
                        document.getElementById('nextPage').disabled = data.currentPage >= data.totalPages;
                        currentPage = data.currentPage;
                    })
                    .catch(error => console.error('Error fetching inventory data:', error));
            }

            document.getElementById('nextPage').addEventListener('click', () => {
                fetchInventoryData(currentPage + 1);
            });

            document.getElementById('prevPage').addEventListener('click', () => {
                fetchInventoryData(currentPage - 1);
            });

            document.getElementById('searchInput').addEventListener('input', (event) => {
                const search = event.target.value;
                fetchInventoryData(1, search);
            });

            fetchInventoryData(currentPage);
        });
    </script>
</body>
</html>
