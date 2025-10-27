<?php
// Database connection
$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "final_project";  // Replace with your actual database name

try {
    // Create PDO connection
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

// Delete cost functionality
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Prepare and execute the delete query
    $stmt = $pdo->prepare("DELETE FROM costs WHERE id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();

    // Reassign IDs to ensure continuous numbering and reset AUTO_INCREMENT
    $pdo->query("SET @i := 0");
    $pdo->query("UPDATE costs SET id = @i := (@i + 1) ORDER BY id");
    $pdo->query("ALTER TABLE costs AUTO_INCREMENT = 1");

    // Redirect after deletion and ID update
    header("Location: costs.php?page=" . $page);
    exit;
}

// Fetch costs with pagination
$stmt = $pdo->prepare("SELECT * FROM costs ORDER BY id LIMIT :offset, :records_per_page");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$costs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total records for pagination
$stmt = $pdo->query("SELECT COUNT(*) FROM costs");
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Costs</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/cost.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
           <ul>
                <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="inventory.php" class="active"><i class="fas fa-boxes"></i> Inventory</a></li>
                <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
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
            <img src="../assets/logo.jpg" alt="Logo" style="height: 50px;">
          </div>
          <div class="search-bar">
            <input type="text" placeholder="Search Cost Data">
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

      <h1>Costs</h1>
      <a href="costForm.php"><button class="add-cost-btn">Add New Cost Entry</button></a>

      <!-- Cost Data Table -->
      <table class="cost-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($costs) > 0): ?>
            <?php foreach ($costs as $cost): ?>
              <tr>
                <td><?php echo htmlspecialchars($cost['id']); ?></td>
                <td><?php echo htmlspecialchars($cost['costCategory']); ?></td>
                <td><?php echo htmlspecialchars($cost['costDescription']); ?></td>
                <td>LKR <?php echo htmlspecialchars(number_format($cost['costAmount'], 2)); ?></td>
                <td><?php echo htmlspecialchars($cost['costDate']); ?></td>
                <td><?php echo htmlspecialchars($cost['created_at']); ?></td>
                <td>
                  <a href="edit-cost.php?id=<?php echo $cost['id']; ?>"><button class="edit-btn">Edit</button></a>
                  <a href="costs.php?delete_id=<?php echo $cost['id']; ?>" onclick="return confirm('Are you sure you want to delete this cost?');"><button class="delete-btn">Delete</button></a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7">No costs found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Pagination Controls -->
      <div class="pagination">
        <?php if ($page > 1): ?>
          <button id="prevPage">
            <a href="costs.php?page=<?php echo $page - 1; ?>">Previous</a>
          </button>
        <?php endif; ?>
        <span id="currentPage">Page <?php echo $page; ?></span>
        <?php if ($page < $total_pages): ?>
          <button id="nextPage">
            <a href="costs.php?page=<?php echo $page + 1; ?>">Next</a>
          </button>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>
