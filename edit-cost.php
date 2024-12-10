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

// Fetch cost details for editing
if (isset($_GET['id'])) {
    $costId = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM costs WHERE id = :id");
    $stmt->bindParam(':id', $costId, PDO::PARAM_INT);
    $stmt->execute();
    $cost = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cost) {
        echo "Cost record not found.";
        exit;
    }
} else {
    echo "No cost ID provided.";
    exit;
}

// Update cost functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $costRate = $_POST['costRate'];
    $amount = $_POST['amount'];

    // Update the cost record in the database
    $stmt = $pdo->prepare("UPDATE costs SET cost_rate = :cost_rate, amount = :amount WHERE id = :id");
    $stmt->bindParam(':cost_rate', $costRate);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':id', $costId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirect to costs page after successful update
        header("Location: costs.php");
        exit;
    } else {
        echo "Error: Could not update cost record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Cost</title>
  <link rel="stylesheet" href="styles/sidebar.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/edit-cost.css">
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
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <button id="logout-btn" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header>
        <div class="top-bar">
          <div class="logo">
            <img src="assets/logo.jpg" alt="Logo">
          </div>
        </div>
      </header>

      <h1>Edit Cost</h1>
      <form class="edit-cost-form" method="POST" action="edit-cost.php?id=<?php echo $cost['id']; ?>">
        <div class="form-group">
          <label for="costRate">Cost Rate (%)</label>
          <input type="number" id="costRate" name="costRate" value="<?php echo htmlspecialchars($cost['cost_rate']); ?>" required>
        </div>
        <div class="form-group">
          <label for="amount">Amount (LKR)</label>
          <input type="number" id="amount" name="amount" value="<?php echo htmlspecialchars($cost['amount']); ?>" required>
        </div>
        <div class="form-group">
          <button type="submit">Save Changes</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
