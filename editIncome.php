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

// Fetch income details for editing
if (isset($_GET['id'])) {
    $incomeId = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM income WHERE id = :id");
    $stmt->bindParam(':id', $incomeId, PDO::PARAM_INT);
    $stmt->execute();
    $income = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$income) {
        echo "Income record not found.";
        exit;
    }
} else {
    echo "Invalid request. No ID provided.";
    exit;
}

// Update income functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $incomeRate = $_POST['income_rate'] ?? null;
    $incomeAmount = $_POST['income_amount'] ?? null;

    if ($incomeRate !== null && $incomeAmount !== null) {
        // Update the income record in the database
        $stmt = $pdo->prepare("
            UPDATE income 
            SET income_rate = :income_rate, income_amount = :income_amount 
            WHERE id = :id
        ");
        $stmt->bindParam(':income_rate', $incomeRate);
        $stmt->bindParam(':income_amount', $incomeAmount);
        $stmt->bindParam(':id', $incomeId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redirect to income page after successful update
            header("Location: income.php");
            exit;
        } else {
            echo "Error: Could not update income record.";
        }
    } else {
        echo "Error: All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Income</title>
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
        <li><a href="income.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
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
          <div class="logo"><img src="assets/logo.jpg" alt="Logo"></div>
        </div>
      </header>

      <h1>Edit Income</h1>

      <!-- Edit Income Form -->
      <form method="POST">
        <div class="form-group">
          <label for="income_rate">Income Rate (%)</label>
          <input type="number" id="income_rate" name="income_rate" value="<?php echo htmlspecialchars($income['income_rate']); ?>" step="0.01" required>
        </div>

        <div class="form-group">
          <label for="income_amount">Amount (LKR)</label>
          <input type="number" id="income_amount" name="income_amount" value="<?php echo htmlspecialchars($income['income_amount']); ?>" step="0.01" required>
        </div>

        <div class="form-group">
          <button type="submit">Save Changes</button>
        </div>
      </form>
    </main>
  </div>

  <script src="scripts/income.js"></script>
</body>
</html>
