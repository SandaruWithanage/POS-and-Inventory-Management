<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

// Create connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Pagination setup
$itemsPerPage = 10; // Number of items to display per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$search = isset($_GET['search']) ? $_GET['search'] : ''; // Search term
$offset = ($page - 1) * $itemsPerPage;

// Fetch inventory data with pagination
$sql = "SELECT * FROM inventory WHERE product_name LIKE :searchTerm ORDER BY id ASC LIMIT :offset, :itemsPerPage";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
$stmt->execute();
$inventoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total number of records for pagination
$totalSql = "SELECT COUNT(*) as total FROM inventory WHERE product_name LIKE :searchTerm";
$totalStmt = $conn->prepare($totalSql);
$totalStmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
$totalStmt->execute();
$totalRow = $totalStmt->fetch(PDO::FETCH_ASSOC);
$totalRecords = $totalRow['total'];

// Calculate total pages
$totalPages = ceil($totalRecords / $itemsPerPage);

// Delete the item when requested
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    $deleteSql = "DELETE FROM inventory WHERE id = :id";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bindParam(':id', $deleteId, PDO::PARAM_INT);
    $deleteStmt->execute();
    header("Location: inventory.php"); // Redirect to avoid resubmission on refresh
    exit();
}

// Renumber the inventory IDs after deletion
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_inventory'])) {
    // Re-sequence inventory IDs after deletion
    $reSequenceSql = "SET @rank := 0; UPDATE inventory SET id = (@rank := @rank + 1);";
    $conn->query($reSequenceSql);

    echo json_encode([
        'data' => $inventoryData,
        'currentPage' => $page,
        'totalPages' => $totalPages
    ]);
    exit();
}

$conn = null;
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
            <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
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
                <button id="prevPage" style="display: none;">Previous</button>
                <span id="currentPage">Page 1</span>
                <button id="nextPage" style="display: none;">Next</button>
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
                                <td>${item.quantity}</td>
                                <td>${item.unit_price}</td>
                                <td>${item.selling_price}</td>
                                <td>${(item.quantity * item.selling_price).toFixed(2)}</td>
                                <td>
                                    <a href="edit-inventory.php?id=${item.id}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    |
                                    <a href="inventory.php?delete_id=${item.id}">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            `;
                            tableBody.appendChild(row);
                        });

                        document.getElementById('currentPage').textContent = `Page ${data.currentPage}`;
                        
                        // Show/Hide Pagination Buttons
                        if (data.currentPage > 1) {
                            document.getElementById('prevPage').style.display = 'inline-block';
                        } else {
                            document.getElementById('prevPage').style.display = 'none';
                        }

                        if (data.currentPage < data.totalPages) {
                            document.getElementById('nextPage').style.display = 'inline-block';
                        } else {
                            document.getElementById('nextPage').style.display = 'none';
                        }

                        currentPage = data.currentPage;
                    })
                    .catch(error => console.error('Error fetching inventory data:', error));
            }

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
