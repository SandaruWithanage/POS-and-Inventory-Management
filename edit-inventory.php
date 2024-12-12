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

// Fetch inventory details for editing
if (isset($_GET['id'])) {
    $inventoryId = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = :id");
    $stmt->bindParam(':id', $inventoryId, PDO::PARAM_INT);
    $stmt->execute();
    $inventory = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$inventory) {
        echo "Inventory record not found.";
        exit;
    }
} else {
    echo "Invalid request. No ID provided.";
    exit;
}

// Update inventory functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = $_POST['product_name'] ?? null;
    $barcodeNo = $_POST['barcode_no'] ?? null;
    $category = $_POST['category'] ?? null;
    $quantity = $_POST['quantity'] ?? null;
    $unitPrice = $_POST['unit_price'] ?? null;
    $sellingPrice = $_POST['selling_price'] ?? null;

    // Calculate total value
    $totalValue = $quantity * $sellingPrice;

    if ($productName !== null && $barcodeNo !== null && $category !== null && $quantity !== null && $unitPrice !== null && $sellingPrice !== null) {
        // Update the inventory record in the database
        $stmt = $pdo->prepare("
            UPDATE inventory 
            SET product_name = :product_name, barcode_no = :barcode_no, category = :category, quantity = :quantity, unit_price = :unit_price, selling_price = :selling_price, total_value = :total_value
            WHERE id = :id
        ");
        $stmt->bindParam(':product_name', $productName);
        $stmt->bindParam(':barcode_no', $barcodeNo);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':unit_price', $unitPrice);
        $stmt->bindParam(':selling_price', $sellingPrice);
        $stmt->bindParam(':total_value', $totalValue);
        $stmt->bindParam(':id', $inventoryId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redirect to inventory page after successful update
            header("Location: inventory.php");
            exit;
        } else {
            echo "Error: Could not update inventory record.";
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
  <title>Edit Inventory</title>
  <link rel="stylesheet" href="styles/sidebar.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/inventory.css">
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
          <div class="logo"><img src="assets/logo.jpg" alt="Logo"></div>
        </div>
      </header>

      <h1>Edit Inventory</h1>

      <!-- Edit Inventory Form -->
      <form method="POST">
        <div class="form-group">
          <label for="product_name">Product Name</label>
          <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($inventory['product_name']); ?>" required>
        </div>

        <div class="form-group">
          <label for="barcode_no">Barcode No</label>
          <input type="text" id="barcode_no" name="barcode_no" value="<?php echo htmlspecialchars($inventory['barcode_no']); ?>" required>
        </div>

        <div class="form-group">
          <label for="category">Category</label>
          <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($inventory['category']); ?>" required>
        </div>

        <div class="form-group">
          <label for="quantity">Quantity</label>
          <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($inventory['quantity']); ?>" required>
        </div>

        <div class="form-group">
          <label for="unit_price">Unit Price (LKR)</label>
          <input type="number" id="unit_price" name="unit_price" value="<?php echo htmlspecialchars($inventory['unit_price']); ?>" step="0.01" required>
        </div>

        <div class="form-group">
          <label for="selling_price">Selling Price (LKR)</label>
          <input type="number" id="selling_price" name="selling_price" value="<?php echo htmlspecialchars($inventory['selling_price']); ?>" step="0.01" required>
        </div>

        <div class="form-group">
          <label for="total_value">Total Value (LKR)</label>
          <input type="number" id="total_value" name="total_value" value="<?php echo htmlspecialchars($inventory['total_value']); ?>" disabled>
        </div>

        <div class="form-group">
          <button type="submit">Save Changes</button>
        </div>
      </form>
    </main>
  </div>

  <script src="scripts/inventory.js"></script>
</body>
</html>
