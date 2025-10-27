<?php
// Database connection
$servername = "localhost";
$username = "root";  // Database username
$password = "";      // Database password
$dbname = "final_project";  // Database name

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// ✅ UPDATED QUERY — match structure used by inventory system
$stmt = $pdo->prepare("
    SELECT id, supplierName, supplierEmail, supplierPhone, productSupplied, productQuantity
    FROM suppliers
    ORDER BY supplierName ASC
    LIMIT :offset, :records_per_page
");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of records for pagination (unchanged)
$stmt = $pdo->query("SELECT COUNT(*) FROM suppliers");
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

// Delete record functionality (unchanged)
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();

    // Reassign IDs to ensure continuous numbering
    $pdo->query("SET @i := 0");
    $pdo->query("UPDATE suppliers SET id = (@i := @i + 1)");
    $pdo->query("ALTER TABLE suppliers AUTO_INCREMENT = 1");

    header("Location: suppliers.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suppliers</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/supplier.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.php" class="active"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
        <li><a href="reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
      </ul>
      <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
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

      <h1>Suppliers</h1>

      <div class="table-header">
        <a href="suppliersForm.php">
          <button id="addSupplierBtn">Add New Supplier</button>
        </a>
      </div>

      <!-- Suppliers Table -->
      <table id="suppliersTable">
        <thead>
          <tr>
            <th>Supplier ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Product Supplied</th>
            <th>Product Quantity</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($suppliers as $supplier): ?>
            <tr>
              <td><?php echo htmlspecialchars($supplier['id']); ?></td>
              <td><?php echo htmlspecialchars($supplier['supplierName']); ?></td>
              <td><?php echo htmlspecialchars($supplier['supplierEmail']); ?></td>
              <td><?php echo htmlspecialchars($supplier['supplierPhone']); ?></td>
              <td><?php echo htmlspecialchars($supplier['productSupplied']); ?></td>
              <td><?php echo htmlspecialchars($supplier['productQuantity']); ?></td>
              <td>
                <a href="editSupplier.php?id=<?php echo $supplier['id']; ?>" class="edit-btn">
                  <i class="fas fa-edit"></i>
                </a>
                <a href="suppliers.php?delete_id=<?php echo urlencode($supplier['id']); ?>" 
                   class="delete-btn" 
                   onclick="return confirm('Are you sure you want to delete this record?');">
                   <i class="fas fa-trash-alt"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Pagination Controls -->
      <div class="pagination">
        <?php if ($page > 1): ?>
          <button id="prevPage">
            <a href="suppliers.php?page=<?php echo $page - 1; ?>">Previous</a>
          </button>
        <?php endif; ?>

        <span id="currentPage">Page <?php echo $page; ?></span>

        <?php if ($page < $total_pages): ?>
          <button id="nextPage">
            <a href="suppliers.php?page=<?php echo $page + 1; ?>">Next</a>
          </button>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>
