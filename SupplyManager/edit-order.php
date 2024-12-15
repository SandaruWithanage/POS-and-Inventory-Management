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

// Fetch order details for editing
if (isset($_GET['id'])) {
    $orderId = $_GET['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
    $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "Order not found.";
        exit;
    }
}

// Update order functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderName = $_POST['orderName'];
    $orderDate = $_POST['orderDate'];
    $orderStatus = $_POST['orderStatus'];
    $orderValue = $_POST['orderValue'];

    // Update the order in the database
    $stmt = $pdo->prepare("UPDATE orders SET order_name = :order_name, order_date = :order_date, 
                           order_status = :order_status, order_value = :order_value WHERE id = :id");
    $stmt->bindParam(':order_name', $orderName);
    $stmt->bindParam(':order_date', $orderDate);
    $stmt->bindParam(':order_status', $orderStatus);
    $stmt->bindParam(':order_value', $orderValue);
    $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirect to orders page after successful update
        header("Location: orders.php");
        exit;
    } else {
        echo "Error: Could not update order";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Order</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/order-form.css">
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

      <h1>Edit Order</h1>

      <!-- Edit Order Form -->
      <form class="order-form" method="POST" action="edit-order.php?id=<?php echo $order['id']; ?>">
        <div class="form-group">
          <label for="orderName">Order Name</label>
          <input type="text" id="orderName" name="orderName" value="<?php echo htmlspecialchars($order['order_name']); ?>" required>
        </div>

        <div class="form-group">
          <label for="orderDate">Order Date</label>
          <input type="date" id="orderDate" name="orderDate" value="<?php echo htmlspecialchars($order['order_date']); ?>" required>
        </div>

        <div class="form-group">
          <label for="orderStatus">Order Status</label>
          <select id="orderStatus" name="orderStatus" required>
            <option value="processing" <?php echo $order['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
            <option value="completed" <?php echo $order['order_status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
            <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
            <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
          </select>
        </div>

        <div class="form-group">
          <label for="orderValue">Order Value</label>
          <input type="number" id="orderValue" name="orderValue" value="<?php echo htmlspecialchars($order['order_value']); ?>" required>
        </div>

        <div class="form-group">
          <button type="submit">Save Changes</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
