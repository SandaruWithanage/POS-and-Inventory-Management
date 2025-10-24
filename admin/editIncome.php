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

// Fetch income details for editing
if (isset($_GET['id'])) {
    $incomeId = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM income WHERE id = ?");
    $stmt->bind_param("i", $incomeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $income = $result->fetch_assoc();

    if (!$income) {
        echo "<p style='color:red;'>Income record not found.</p>";
        exit;
    }
} else {
    echo "<p style='color:red;'>Invalid request. No ID provided.</p>";
    exit;
}

// Update income functionality
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $salesAmount = $_POST['salesAmount'];
    $costAmount = $_POST['costAmount'];

    // Calculate income amount
    $incomeAmount = $salesAmount - $costAmount;

    // Validate input
    if (empty($salesAmount) || empty($costAmount)) {
        echo "<p style='color:red;'>All fields are required!</p>";
    } else {
        // Update the income record in the database
        $stmt = $conn->prepare("UPDATE income SET sales_amount = ?, cost_amount = ?, income_amount = ? WHERE id = ?");
        $stmt->bind_param("dddi", $salesAmount, $costAmount, $incomeAmount, $incomeId);

        if ($stmt->execute()) {
            // Redirect to income-costs.php after successful update
            header("Location: income-costs.php");
            exit;
        } else {
            echo "<p style='color:red;'>Error: Could not update income record.</p>";
        }

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
  <title>Edit Income</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/income.css">

  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <script>
    // JavaScript to auto-calculate the income amount
    function calculateIncome() {
      var salesAmount = parseFloat(document.getElementById('salesAmount').value) || 0;
      var costAmount = parseFloat(document.getElementById('costAmount').value) || 0;
      var incomeAmount = salesAmount - costAmount;

      // Set the value of the income amount field
      document.getElementById('incomeAmount').value = incomeAmount.toFixed(2);
    }

    // Add event listeners to the sales amount and cost amount fields
    window.onload = function() {
      document.getElementById('salesAmount').addEventListener('input', calculateIncome);
      document.getElementById('costAmount').addEventListener('input', calculateIncome);
    };
  </script>
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

      <h1>Edit Income</h1>

      <!-- Edit Income Form -->
      <form class="edit-income-form" method="POST">
        <div class="form-group">
          <label for="salesAmount">Sales Amount (LKR)</label>
          <input type="number" id="salesAmount" name="salesAmount" value="<?php echo htmlspecialchars($income['sales_amount']); ?>" required>
        </div>

        <div class="form-group">
          <label for="costAmount">Cost Amount (LKR)</label>
          <input type="number" id="costAmount" name="costAmount" value="<?php echo htmlspecialchars($income['cost_amount']); ?>" required>
        </div>

        <div class="form-group">
          <label for="incomeAmount">Income Amount (LKR)</label>
          <input type="number" id="incomeAmount" name="incomeAmount" value="<?php echo htmlspecialchars($income['income_amount']); ?>" readonly>
        </div>

        <div class="form-group">
          <button type="submit">Save Changes</button>
        </div>

        <p class="error-message" id="formErrorMessage"></p>
      </form>
    </main>
  </div>
</body>
</html>
