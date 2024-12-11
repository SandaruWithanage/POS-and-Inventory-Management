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
        echo "Cost not found.";
        exit;
    }
} else {
    echo "Invalid cost ID.";
    exit;
}

// Update cost functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $costCategory = $_POST['costCategory'];
    $costDescription = $_POST['costDescription'];
    $costAmount = $_POST['costAmount'];
    $costDate = $_POST['costDate'];

    // Update the cost in the database
    $stmt = $pdo->prepare("UPDATE costs SET costCategory = :costCategory, costDescription = :costDescription, 
                           costAmount = :costAmount, costDate = :costDate WHERE id = :id");
    $stmt->bindParam(':costCategory', $costCategory);
    $stmt->bindParam(':costDescription', $costDescription);
    $stmt->bindParam(':costAmount', $costAmount);
    $stmt->bindParam(':costDate', $costDate);
    $stmt->bindParam(':id', $costId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirect to costs page after successful update
        header("Location: costs.php");
        exit;
    } else {
        echo "Error: Could not update cost";
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
  <link rel="stylesheet" href="styles/cost.css">
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
            <a href="profile.php">
              <span class="icon"><i class="fas fa-user-circle"></i></span>
            </a>
          </div>
        </div>
      </header>

      <h1>Edit Cost</h1>

      <!-- Edit Cost Form -->
      <form class="cost-form" method="POST" action="edit-cost.php?id=<?php echo $cost['id']; ?>">
        <div class="form-group">
          <label for="costCategory">Cost Category</label>
          <input type="text" id="costCategory" name="costCategory" value="<?php echo htmlspecialchars($cost['costCategory']); ?>" required>
        </div>

        <div class="form-group">
          <label for="costDescription">Cost Description</label>
          <textarea id="costDescription" name="costDescription" required><?php echo htmlspecialchars($cost['costDescription']); ?></textarea>
        </div>

        <div class="form-group">
          <label for="costAmount">Cost Amount</label>
          <input type="number" id="costAmount" name="costAmount" value="<?php echo htmlspecialchars($cost['costAmount']); ?>" required>
        </div>

        <div class="form-group">
          <label for="costDate">Cost Date</label>
          <input type="date" id="costDate" name="costDate" value="<?php echo htmlspecialchars($cost['costDate']); ?>" required>
        </div>

        <div class="form-group">
          <button type="submit">Save Changes</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
