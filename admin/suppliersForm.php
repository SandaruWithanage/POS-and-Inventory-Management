<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project"; // Change this to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplierName = trim($_POST['supplierName']);
    $supplierEmail = trim($_POST['supplierEmail']);
    $supplierPhone = trim($_POST['supplierPhone']);
    $productSupplied = trim($_POST['productSupplied']);
    $productQuantity = !empty($_POST['productQuantity']) ? intval($_POST['productQuantity']) : 0;

    // Validate input
    if (empty($supplierName) || empty($supplierEmail) || empty($supplierPhone) || empty($productSupplied)) {
        echo "<p style='color:red;'>All fields are required!</p>";
    } else {
        // Prepare and bind SQL statement (now includes productQuantity)
        $stmt = $conn->prepare("INSERT INTO suppliers (supplierName, supplierEmail, supplierPhone, productSupplied, productQuantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $supplierName, $supplierEmail, $supplierPhone, $productSupplied, $productQuantity);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect back to suppliers.php
            header("Location: suppliers.php");
            exit();
        } else {
            echo "<p style='color:red;'>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }

        // Close statement
        $stmt->close();
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Supplier</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/supplier.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.php" class="active"><i class="fas fa-truck"></i> Suppliers</a></li>
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
            <a href="profile.html">
              <span class="icon"><i class="fas fa-user-circle"></i></span>
            </a>
          </div>
        </div>
      </header>

      <h1>Add New Supplier</h1>

      <!-- Add Supplier Form -->
      <form class="add-supplier-form" id="addSupplierForm" method="POST">
        <div class="form-group">
          <label for="supplierName">Supplier Name</label>
          <input type="text" id="supplierName" name="supplierName" required>
        </div>

        <div class="form-group">
          <label for="supplierEmail">Email Address</label>
          <input type="email" id="supplierEmail" name="supplierEmail" required>
        </div>

        <div class="form-group">
          <label for="supplierPhone">Phone Number</label>
          <input type="text" id="supplierPhone" name="supplierPhone" required>
        </div>

        <div class="form-group">
          <label for="productSupplied">Products Supplied</label>
          <textarea id="productSupplied" name="productSupplied" required></textarea>
        </div>

        <div class="form-group">
          <label for="productQuantity">Product Quantity</label>
          <input type="number" id="productQuantity" name="productQuantity" min="0" value="0" required>
        </div>

        <div class="form-group">
          <button type="submit">Add Supplier</button>
        </div>

        <p class="error-message" id="formErrorMessage"></p>
      </form>
    </main>
  </div>
</body>
</html>
