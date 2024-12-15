<?php
// Database connection
$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "final_project";  // Replace with your actual database name

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Pagination setup
$records_per_page = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
$offset = ($page - 1) * $records_per_page;

// Delete record functionality
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Prepare and execute the delete query
    $stmt = $pdo->prepare("DELETE FROM income WHERE id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();

    // Reassign IDs to ensure continuous numbering and reset AUTO_INCREMENT
    $pdo->query("SET @i := 0");
    $pdo->query("UPDATE income SET id = @i := (@i + 1) ORDER BY id");
    $pdo->query("ALTER TABLE income AUTO_INCREMENT = 1");

    // Redirect back to the page after deletion and ID update
    header("Location: income-costs.php?page=" . $page);
    exit;
}

// Fetch income records with pagination
$stmt = $pdo->prepare("SELECT * FROM income ORDER BY id LIMIT :offset, :records_per_page");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$incomeRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total records for pagination
$stmt = $pdo->query("SELECT COUNT(*) FROM income");
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Income and Costs</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/income.css">
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
        <li><a href="income-cost.php" class="active"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.php"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purchases.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <button id="logout-btn" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <header>
        <div class="top-bar">
          <div class="logo">
            <img src="../assets/logo.jpg" alt="Logo" style="height: 50px;">
          </div>
          <div class="search-bar">
            <input type="text" placeholder="Type for search">
          </div>
          <div class="user-icons">
            <span class="icon"><i class="fas fa-bell"></i></span>
            <span class="icon"><i class="fas fa-comments"></i></span>
            <a href="profile.php">
              <span class="icon"><i class="fas fa-user-circle"></i></span>
            </a>
          </div>
        </div>
      </header>

      <h1>Income and Costs</h1>

      <!-- Income and Costs Table -->
      <table class="income-cost-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Sales Amount (LKR)</th>
            <th>Cost Amount (LKR)</th>
            <th>Income Amount (LKR)</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($incomeRecords as $income): ?>
            <tr>
              <td><?php echo htmlspecialchars($income['id']); ?></td>
              <td>LKR <?php echo htmlspecialchars(number_format($income['sales_amount'], 2)); ?></td>
              <td>LKR <?php echo htmlspecialchars(number_format($income['cost_amount'], 2)); ?></td>
              <td>LKR <?php echo htmlspecialchars(number_format($income['income_amount'], 2)); ?></td>
              <td><?php echo htmlspecialchars($income['created_at']); ?></td>
              <td>
                <a href="editincome.php?id=<?php echo $income['id']; ?>">
                  <button class="edit-btn">Edit</button>
                </a>
                <a href="income-costs.php?delete_id=<?php echo $income['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                  <button class="delete-btn">Delete</button>
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
            <a href="income-costs.php?page=<?php echo $page - 1; ?>">Previous</a>
          </button>
        <?php endif; ?>

        <span id="currentPage">Page <?php echo $page; ?></span>

        <?php if ($page < $total_pages): ?>
          <button id="nextPage">
            <a href="income-costs.php?page=<?php echo $page + 1; ?>">Next</a>
          </button>
        <?php endif; ?>
      </div>

      <!-- Add Button -->
      <a href="income-costsForm.php"><button class="add-btn">Add New Income-Cost Record</button></a>
    </main>
  </div>
</body>
</html>
