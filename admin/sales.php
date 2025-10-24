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

// Delete sales functionality
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $stmt = $pdo->prepare("DELETE FROM sales WHERE id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect after deletion
    header("Location: sales.php");
    exit;
}

// Fetch sales from the database
$stmt = $pdo->query("SELECT * FROM sales");
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sales</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/sales.css">
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
        <li><a href="sales.php" class="active"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.php"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purchase.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
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

      <h1>Sales</h1>

      <!-- Sales List -->
      <table class="sales-table">
        <thead>
          <tr>
            <th>Sales ID</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Unit Price (LKR)</th>
            <th>Selling Price (LKR)</th>
            <th>Total Amount (LKR)</th>
            <th>Sales Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sales as $sale): ?>
            <tr>
              <td><?php echo htmlspecialchars($sale['id']); ?></td>
              <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
              <td><?php echo htmlspecialchars($sale['quantity']); ?></td>
              <td>LKR <?php echo htmlspecialchars(number_format($sale['unit_price'], 2)); ?></td>
              <td>LKR <?php echo htmlspecialchars(number_format($sale['selling_price'], 2)); ?></td>
              <td>LKR <?php echo htmlspecialchars(number_format($sale['quantity'] * $sale['selling_price'], 2)); ?></td>
              <td><?php echo htmlspecialchars($sale['sales_date']); ?></td>
              <td>
                <a href="edit-sale.php?id=<?php echo $sale['id']; ?>"><button class="edit-btn">Edit</button></a>
                <a href="sales.php?delete_id=<?php echo $sale['id']; ?>" onclick="return confirm('Are you sure you want to delete this sale?');"><button class="delete-btn">Delete</button></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <a href="salesForm.php"><button class="add-sale-btn">Add New Sale</button></a>
    </main>
  </div>
</body>
</html>
