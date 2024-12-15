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
    $order_id = $_POST['order_id'];
    $order_type = $_POST['order_type'];
    $order_date = $_POST['order_date'];

    // Prepare SQL query (using correct table name 'purchase')
    $stmt = $pdo->prepare("INSERT INTO purchase (order_id, order_type, order_date) VALUES (:order_id, :order_type, :order_date)");

    // Check if the preparation was successful
    if ($stmt === false) {
        die("Error preparing the query: " . implode(" ", $pdo->errorInfo()));
    }

    // Bind parameters
    $stmt->bindParam(':order_id', $order_id);
    $stmt->bindParam(':order_type', $order_type);
    $stmt->bindParam(':order_date', $order_date);

    // Execute the query
    if ($stmt->execute()) {
        echo "Purchase added successfully!";
        header("Location: purches.php"); // Redirect after successful insertion
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
        <li><a href="dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="SupplyManager/inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="SupplyManager/suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="SupplyManager/budget.php"><i class="fas fa-money-bill"></i> Budget</a></li>
      <li><a href="SupplyManager/sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="SupplyManager/orders.php" id="ordersMenuItem"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="SupplyManager/shipment.php"><i class="fas fa-shipping-fast"></i> Shipment & Purchase</a></li>
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

      <h1>Add New Purchase</h1>

      <form action="purchaseForm.php" method="POST">
        <div class="form-group">
          <label for="order_id">Order ID:</label>
          <input type="text" id="order_id" name="order_id" required>
        </div>
        <div class="form-group">
          <label for="order_type">Order Type:</label>
          <input type="text" id="order_type" name="order_type" required>
        </div>
        <div class="form-group">
          <label for="order_date">Order Date:</label>
          <input type="date" id="order_date" name="order_date" required>
        </div>
        <button type="submit" class="submit-btn">Add Purchase</button>
      </form>
    </main>
  </div>
</body>
</html>
