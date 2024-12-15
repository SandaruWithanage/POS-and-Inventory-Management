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

// Fetch shipment data from the database
$stmt = $pdo->query("SELECT * FROM shipment ORDER BY shipment_date DESC");
$shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shipment</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/shipment.css">

  <!-- Font Awesome for Icons -->
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
        <li><a href="shipment.php" class="active"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purches.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <header>
        <div class="top-bar">
          <!-- Add logo image -->
          <div class="logo">
            <img src="../assets/logo.jpg" alt="Logo">
          </div>
          <div class="search-bar">
            <input type="text" placeholder="Type for search" id="searchInput">
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

      <h1>Shipment</h1>

      <div class="table-header">
        <a href="shipmentForm.php">
          <button id="addShipmentBtn">Add New Shipment</button>
        </a>
      </div>
      
      <!-- Shipment Table -->
      <table id="shipmentTable">
        <thead>
          <tr>
            <th>Shipment ID</th>
            <th>Shipment Quantity</th>
            <th>Shipment Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($shipments as $shipment): ?>
            <tr>
              <td><?php echo htmlspecialchars($shipment['shipment_id']); ?></td>
              <td><?php echo htmlspecialchars($shipment['shipment_quantity']); ?></td>
              <td><?php echo htmlspecialchars($shipment['shipment_date']); ?></td>
              <td>
                <a href="shipmentForm.php?id=<?php echo $shipment['shipment_id']; ?>"><button class="edit-btn">Edit</button></a>
                <a href="shipment.php?delete_id=<?php echo $shipment['shipment_id']; ?>" onclick="return confirm('Are you sure you want to delete this shipment?');"><button class="delete-btn">Delete</button></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      
      <!-- Pagination Controls -->
      <div class="pagination">
        <button id="prevPage">Previous</button>
        <span id="currentPage">Page 1</span>
        <button id="nextPage">Next</button>
      </div>
    </main>
  </div>

  <script src="scripts/shipment.js"></script>
</body>
</html>
