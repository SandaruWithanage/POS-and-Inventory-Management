<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project"; // Update this to your actual database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $productName = $_POST['productName'];
    $quantity = intval($_POST['quantity']);
    $salesDate = $_POST['salesDate'];
    $totalAmount = floatval($_POST['totalAmount']);

    // Validate the inputs
    if (empty($productName) || $quantity <= 0 || empty($salesDate) || $totalAmount <= 0) {
        echo "<script>alert('All fields are required and must be valid.');</script>";
    } else {
        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO sales (product_name, quantity, sales_date, total_amount) VALUES (?, ?, ?, ?)");
        
        if ($stmt === false) {
            echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
        } else {
            // Bind parameters to the statement
            $stmt->bind_param("sids", $productName, $quantity, $salesDate, $totalAmount);

            // Execute the query
            if ($stmt->execute()) {
                echo "<script>alert('Sale added successfully!'); window.location.href = 'sales.php';</script>";
            } else {
                echo "<script>alert('Error: " . $stmt->error . "');</script>";
            }

            // Close the statement
            $stmt->close();
        }
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
  <title>Sales Management</title>
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
        <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.php"><i class="fas fa-coins"></i> Budget</a></li>
        <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.php"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
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
            <a href="profile.html">
              <span class="icon"><i class="fas fa-user-circle"></i></span>
            </a>
          </div>
        </div>
      </header>

      <h1>Add New Sale</h1>

      <!-- Add Sale Form -->
      <form class="add-sale-form" id="addSaleForm" method="POST">
        <div class="form-group">
          <label for="productName">Product Name</label>
          <input type="text" id="productName" name="productName" placeholder="e.g., Laptop, Chair" required>
        </div>

        <div class="form-group">
          <label for="quantity">Quantity</label>
          <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" required>
        </div>

        <div class="form-group">
          <label for="salesDate">Sales Date</label>
          <input type="date" id="salesDate" name="salesDate" required>
        </div>

        <div class="form-group">
          <label for="totalAmount">Total Amount</label>
          <input type="number" id="totalAmount" name="totalAmount" placeholder="e.g., 5000.75" required>
        </div>

        <div class="form-group">
          <button type="submit">Add Sale</button>
        </div>

        <p class="error-message" id="formErrorMessage"></p>
      </form>
    </main>
  </div>
</body>
</html>
