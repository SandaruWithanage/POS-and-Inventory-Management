<?php
// Database connection (ensure your connection is correct)
$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "final_project";  // Replace with your actual database name

// Establish database connection using PDO
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission and insert data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data (make sure to sanitize inputs as needed)
    $order_name = $_POST['order_name'];
    $order_date = $_POST['order_date'];
    $order_status = $_POST['order_status'];
    $order_value = $_POST['order_value'];

    // Prepare SQL query (using correct table name 'purchase')
    $stmt = $pdo->prepare("INSERT INTO purchase (order_name, order_date, order_status, order_value) 
                           VALUES (:order_name, :order_date, :order_status, :order_value)");

    // Bind parameters
    $stmt->bindParam(':order_name', $order_name);
    $stmt->bindParam(':order_date', $order_date);
    $stmt->bindParam(':order_status', $order_status);
    $stmt->bindParam(':order_value', $order_value);

    // Execute the query
    if ($stmt->execute()) {
        header("Location: purchase.php"); // Redirect after successful insertion
        exit;
    } else {
        echo "Error executing the query: " . implode(" ", $stmt->errorInfo());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Purchase</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/purchase.css">
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

      <h1>Add New Purchase</h1>

      <form action="purchaseForm.php" method="POST">
        <div class="form-group">
          <label for="order_name">Order Name:</label>
          <input type="text" id="order_name" name="order_name" required>
        </div>
        <div class="form-group">
          <label for="order_date">Order Date:</label>
          <input type="date" id="order_date" name="order_date" required>
        </div>
        <div class="form-group">
          <label for="order_status">Order Status:</label>
          <input type="text" id="order_status" name="order_status" required>
        </div>
        <div class="form-group">
          <label for="order_value">Order Value:</label>
          <input type="number" id="order_value" name="order_value" required>
        </div>
        <button type="submit" class="submit-btn">Add Purchase</button>
      </form>
    </main>
  </div>
</body>
</html>
