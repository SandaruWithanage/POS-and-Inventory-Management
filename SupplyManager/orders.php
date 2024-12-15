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
$records_per_page = 10; // Number of records to display per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
$offset = ($page - 1) * $records_per_page;

// Delete order functionality
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect after deletion
    header("Location: orders.php");
    exit;
}

// Fetch orders from the database with pagination
$stmt = $pdo->prepare("SELECT * FROM orders LIMIT :offset, :records_per_page");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of records for pagination
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/orders.css">
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

      <h1>Orders</h1>

      <!-- Order List -->
      <table class="order-table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Order Name</th>
            <th>Order Date</th>
            <th>Order Status</th>
            <th>Order Value</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td><?php echo htmlspecialchars($order['id']); ?></td>
              <td><?php echo htmlspecialchars($order['order_name']); ?></td>
              <td><?php echo htmlspecialchars($order['order_date']); ?></td>
              <td><?php echo htmlspecialchars($order['order_status']); ?></td>
              <td>LKR <?php echo htmlspecialchars(number_format($order['order_value'], 2)); ?></td>
              <td>
                <a href="edit-order.php?id=<?php echo $order['id']; ?>"><button class="edit-btn">Edit</button></a>
                <a href="orders.php?delete_id=<?php echo $order['id']; ?>" onclick="return confirm('Are you sure you want to delete this order?');"><button class="delete-btn">Delete</button></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Pagination Controls -->
      <div class="pagination">
        <?php if ($page > 1): ?>
          <button id="prevPage">
            <a href="orders.php?page=<?php echo $page - 1; ?>">Previous</a>
          </button>
        <?php endif; ?>

        <span id="currentPage">Page <?php echo $page; ?></span>

        <?php if ($page < $total_pages): ?>
          <button id="nextPage">
            <a href="orders.php?page=<?php echo $page + 1; ?>">Next</a>
          </button>
        <?php endif; ?>
      </div>

      <a href="add-order.php"><button class="add-order-btn">Add New Order</button></a>
    </main>
  </div>
</body>
</html>
