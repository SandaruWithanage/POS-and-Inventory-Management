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

// Fetch budget details for editing
if (isset($_GET['id'])) {
    $budgetId = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM budget WHERE id = :id");
    $stmt->bindParam(':id', $budgetId, PDO::PARAM_INT);
    $stmt->execute();
    $budget = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$budget) {
        echo "Budget record not found.";
        exit;
    }
} else {
    echo "Invalid request. No ID provided.";
    exit;
}

// Update budget functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $startDate = $_POST['start_date'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $budgetRate = $_POST['budget_rate'] ?? null;
    $endDate = $_POST['end_date'] ?? null;

    if ($startDate !== null && $amount !== null && $budgetRate !== null && $endDate !== null) {
        // Update the budget record in the database
        $stmt = $pdo->prepare("
            UPDATE budget 
            SET start_date = :start_date, amount = :amount, budget_rate = :budget_rate, end_date = :end_date
            WHERE id = :id
        ");
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':budget_rate', $budgetRate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':id', $budgetId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redirect to budget page after successful update
            header("Location: budget.php");
            exit;
        } else {
            echo "Error: Could not update budget record.";
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
  <title>Edit Budget</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/budgets.css">
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
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
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
          <div class="logo"><img src="../assets/logo.jpg" alt="Logo"></div>
        </div>
      </header>

      <h1>Edit Budget</h1>

      <!-- Edit Budget Form -->
      <form method="POST">
        <div class="form-group">
          <label for="start_date">Start Date</label>
          <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($budget['start_date']); ?>" required>
        </div>

        <div class="form-group">
          <label for="amount">Amount (LKR)</label>
          <input type="number" id="amount" name="amount" value="<?php echo htmlspecialchars($budget['amount']); ?>" step="0.01" required>
        </div>

        <div class="form-group">
          <label for="budget_rate">Budget Rate (%)</label>
          <input type="number" id="budget_rate" name="budget_rate" value="<?php echo htmlspecialchars($budget['budget_rate']); ?>" step="0.01" required>
          <span>%</span> <!-- Display percentage symbol next to input -->
        </div>

        <div class="form-group">
          <label for="end_date">End Date</label>
          <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($budget['end_date']); ?>" required>
        </div>

        <div class="form-group">
          <button type="submit">Save Changes</button>
        </div>
      </form>
    </main>
  </div>

  <script src="scripts/budget.js"></script>
</body>
</html>
