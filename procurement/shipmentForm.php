<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";     // Replace with your database password
$dbname = "final_project"; // Replace with your database name

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Initialize error message
$errorMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $shipmentQuantity = $_POST['shipmentquantity']; // Match the form field name
  $shipmentDate = $_POST['shipmentDate'];
  $productDetails = $_POST['productDetails'];

  // Check if all fields are filled
  if (!empty($shipmentQuantity) && !empty($shipmentDate) && !empty($productDetails)) {
      // Insert data into the database
      $sql = "INSERT INTO shipment (shipment_quantity, shipment_date, product_details) 
              VALUES (:shipment_quantity, :shipment_date, :product_details)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
          ':shipment_quantity' => $shipmentQuantity,
          ':shipment_date' => $shipmentDate,
          ':product_details' => $productDetails
      ]);
      // Success message
      echo "<script>alert('Shipment added successfully!');</script>";
  } else {
      $errorMessage = "All fields are required.";
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shipment Management</title>
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
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income </a></li>
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.php" class="active"><i class="fas fa-shipping-fast"></i> Shipment </a></li>
        <li><a href="purches.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
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

      <h1>Add New Shipment</h1>

      <!-- Add Shipment Form -->
      <form class="add-shipment-form" id="addShipmentForm" method="POST">


        <div class="form-group">
          <label for="shipmentQuantity">shipmentquantity</label>
          <input type="text" id="shipmentquantity" name="shipmentquantity" required>
        </div>

        <div class="form-group">
          <label for="shipmentDate">Shipment Date</label>
          <input type="date" id="shipmentDate" name="shipmentDate" required>
        </div>

        <div class="form-group">
          <label for="productDetails">Product Details</label>
          <textarea id="productDetails" name="productDetails" required></textarea>
        </div>

        <div class="form-group">
          <button type="submit">Add Shipment</button>
        </div>

        <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
      </form>
    </main>
  </div>
</body>
</html>
