<?php
// Database connection
$servername = "localhost";  // Replace with your database server
$username = "root";         // Replace with your database username
$password = "";             // Replace with your database password
$dbname = "final_project";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get form data
  $startDate = $_POST['startDate'];
  $amount = $_POST['amount'];
  $budgetRate = $_POST['budgetRate'];
  $endDate = $_POST['endDate'];

  // Validate the input
  if (!empty($startDate) && !empty($amount) && !empty($budgetRate) && !empty($endDate)) {
    // Prepare the SQL query
    $sql = "INSERT INTO budget (start_date, amount, budget_rate, end_date) 
            VALUES ('$startDate', '$amount', '$budgetRate', '$endDate')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
      // After successful insertion, redirect to the budget.php page
      header("Location: budget.php");
      exit; // Make sure the script stops after the redirect
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  } else {
    echo "All fields are required.";
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Budget</title>
  <link rel="stylesheet" href="styles/sidebar.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/budgets.css">

  <!-- Font Awesome for Icons -->
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

      <h1>Add New Budget</h1>

      <!-- Add Budget Form -->
      <form class="add-budget-form" method="POST">
        <div class="form-group">
          <label for="startDate">Start Date</label>
          <input type="date" id="startDate" name="startDate" required>
        </div>

        <div class="form-group">
          <label for="amount">Amount (LKR)</label>
          <input type="number" id="amount" name="amount" required>
        </div>

        <div class="form-group">
          <label for="budgetRate">Budget Rate (%)</label>
          <input type="number" id="budgetRate" name="budgetRate" required>
        </div>

        <div class="form-group">
          <label for="endDate">End Date</label>
          <input type="date" id="endDate" name="endDate" required>
        </div>

        <div class="form-group">
          <button type="submit">Add Budget</button>
        </div>

        <p class="error-message" id="formErrorMessage"></p>
      </form>
    </main>
  </div>
</body>
</html> 
