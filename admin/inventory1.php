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

// Pagination setup
$itemsPerPage = 10; // Number of items to display per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$search = isset($_GET['search']) ? $_GET['search'] : ''; // Search term
$offset = ($page - 1) * $itemsPerPage;

// Fetch inventory data
$sql = "SELECT * FROM inventory WHERE product_name LIKE ? LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("sii", $searchTerm, $offset, $itemsPerPage);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total number of records for pagination
$totalSql = "SELECT COUNT(*) as total FROM inventory WHERE product_name LIKE ?";
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
    <link rel="stylesheet" href="../styles/sidebar.css">
    <link rel="stylesheet" href="../styles/topbar.css">
    <link rel="stylesheet" href="../styles/inventory.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul>
            <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
            <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
            <li><a href="budget.php"><i class="fas fa-coins"></i> Budget</a></li>
            <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
            <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
            <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
            <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
            <li><a href="shipment.php"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
            <li><a href="purchases.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
            <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
            </ul>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </aside>
        <!-- Main Content -->
        <main class="main-content">
            <header>
                <div class="top-bar">
                    <div class="logo">
                        <img src="../assets/logo.jpg" alt="Logo">
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

            <!-- Inventory Table -->
            <table id="inventoryTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Barcode No:</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Selling Price</th>
                        <th>Total Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic inventory data will be inserted here -->
                </tbody>
            </table>

            <!-- Pagination Controls -->
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

            // Function to fetch and display inventory data
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
                                <td><input type="number" class="quantity" data-id="${item.id}" value="${item.quantity}" /></td>
                                <td><input type="number" class="unit_price" data-id="${item.id}" value="${item.unit_price}" step="0.01" /></td>
                                <td><input type="number" class="selling_price" data-id="${item.id}" value="${item.selling_price}" step="0.01" /></td>
                                <td><input type="text" class="total_value" value="${(item.quantity * item.unit_price).toFixed(2)}" readonly /></td>
                                <td>
                                    <a href="edit-inventory.php?id=${item.id}">
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

            // Update total value when quantity, unit price, or selling price is changed
            document.querySelector('#inventoryTable').addEventListener('input', function(e) {
                const target = e.target;
                if (target.classList.contains('quantity') || target.classList.contains('unit_price') || target.classList.contains('selling_price')) {
                    const row = target.closest('tr');
                    const quantity = row.querySelector('.quantity').value;
                    const unitPrice = row.querySelector('.unit_price').value;
                    const sellingPrice = row.querySelector('.selling_price').value;

                    const totalValue = (quantity * unitPrice).toFixed(2);
                    row.querySelector('.total_value').value = totalValue;
                }
            });

            // Pagination Controls
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
