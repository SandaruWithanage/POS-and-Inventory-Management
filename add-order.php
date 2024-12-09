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

// Add order functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderName = $_POST['orderName'];
    $orderDate = $_POST['orderDate'];
    $orderStatus = $_POST['orderStatus'];
    $orderValue = $_POST['orderValue'];

    // Insert new order into the database
    $stmt = $pdo->prepare("INSERT INTO orders (order_name, order_date, order_status, order_value) 
                           VALUES (:order_name, :order_date, :order_status, :order_value)");
    $stmt->bindParam(':order_name', $orderName);
    $stmt->bindParam(':order_date', $orderDate);
    $stmt->bindParam(':order_status', $orderStatus);
    $stmt->bindParam(':order_value', $orderValue);

    if ($stmt->execute()) {
        // Redirect to orders page after successful insertion
        header("Location: orders.php");
        exit;
    } else {
        echo "Error: Could not add order";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Order</title>
  <link rel="stylesheet" href="styles/sidebar.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/order-form.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.php"><i class="fas fa-coins"></i> Budget</a></li>
        <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
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
            <img src="assets/logo.jpg" alt="Logo" style="height: 50px;">
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

      <h1>Add New Order</h1>

      <!-- Add Order Form -->
      <form class="order-form" method="POST" action="add-order.php">
        <div class="form-group">
          <label for="orderName">Order Name</label>
          <input type="text" id="orderName" name="orderName" required>
        </div>

        <div class="form-group">
          <label for="orderDate">Order Date</label>
          <input type="date" id="orderDate" name="orderDate" required>
        </div>

        <div class="form-group">
          <label for="orderStatus">Order Status</label>
          <select id="orderStatus" name="orderStatus" required>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="shipped">Shipped</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>

        <div class="form-group">
          <label for="orderValue">Order Value</label>
          <input type="number" id="orderValue" name="orderValue" required>
        </div>

        <div class="form-group">
          <button type="submit">Add Order</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
