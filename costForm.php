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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $costCategory = $_POST['costCategory'];
    $costDescription = $_POST['costDescription'];
    $costAmount = $_POST['costAmount'];
    $costDate = $_POST['costDate'];

    // Validate the inputs (basic validation)
    if (empty($costCategory) || empty($costDescription) || empty($costAmount) || empty($costDate)) {
        echo "All fields are required.";
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO costs (costCategory, costDescription, costAmount, costDate) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            echo "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param("ssds", $costCategory, $costDescription, $costAmount, $costDate);

            // Execute the query
            if ($stmt->execute()) {
                echo "<script>alert('Cost added successfully!'); window.location.href = 'costs.php';</script>";
            } else {
                echo "Error: " . $stmt->error;
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
  <title>Cost Management</title>
  <link rel="stylesheet" href="styles/sidebar.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/supplier.css">
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
        <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
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
            <a href="profile.html">
              <span class="icon"><i class="fas fa-user-circle"></i></span>
            </a>
          </div>
        </div>
      </header>

      <h1>Add New Cost</h1>

      <!-- Add Cost Form -->
      <form class="add-cost-form" id="addCostForm" method="POST">
        <div class="form-group">
          <label for="costCategory">Category</label>
          <input type="text" id="costCategory" name="costCategory" placeholder="e.g., Utilities, Salaries" required>
        </div>

        <div class="form-group">
          <label for="costDescription">Description</label>
          <textarea id="costDescription" name="costDescription" placeholder="Enter cost description" required></textarea>
        </div>

        <div class="form-group">
          <label for="costAmount">Amount</label>
          <input type="number" id="costAmount" name="costAmount" placeholder="e.g., 120.50" required>
        </div>

        <div class="form-group">
          <label for="costDate">Date</label>
          <input type="date" id="costDate" name="costDate" required>
        </div>

        <div class="form-group">
          <button type="submit">Add Cost</button>
        </div>

        <p class="error-message" id="formErrorMessage"></p>
      </form>
    </main>
  </div>

  
</body>
</html>
