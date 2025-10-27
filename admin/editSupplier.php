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

// Fetch supplier details for editing
if (isset($_GET['id'])) {
    $supplierId = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = ?");
    $stmt->bind_param("i", $supplierId);
    $stmt->execute();
    $result = $stmt->get_result();
    $supplier = $result->fetch_assoc();

    if (!$supplier) {
        echo "<p style='color:red;'>Supplier record not found.</p>";
        exit;
    }
} else {
    echo "<p style='color:red;'>Invalid request. No ID provided.</p>";
    exit;
}

// Update supplier functionality
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
        // Update the supplier record in the database (including productQuantity)
        $stmt = $conn->prepare("UPDATE suppliers SET supplierName = ?, supplierEmail = ?, supplierPhone = ?, productSupplied = ?, productQuantity = ? WHERE id = ?");
        $stmt->bind_param("ssssii", $supplierName, $supplierEmail, $supplierPhone, $productSupplied, $productQuantity, $supplierId);

        if ($stmt->execute()) {
            // Redirect to suppliers.php after successful update
            header("Location: suppliers.php");
            exit;
        } else {
            echo "<p style='color:red;'>Error: Could not update supplier record.</p>";
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
  <title>Edit Supplier</title>
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

      <h1>Edit Supplier</h1>

      <!-- Edit Supplier Form -->
      <form class="edit-supplier-form" id="editSupplierForm" method="POST">

        <div class="form-group">
          <label for="supplierName">Supplier Name</label>
          <input type="text" id="supplierName" name="supplierName" value="<?php echo htmlspecialchars($supplier['supplierName']); ?>" required>
        </div>

        <div class="form-group">
          <label for="supplierEmail">Email Address</label>
          <input type="email" id="supplierEmail" name="supplierEmail" value="<?php echo htmlspecialchars($supplier['supplierEmail']); ?>" required>
        </div>

        <div class="form-group">
          <label for="supplierPhone">Phone Number</label>
          <input type="text" id="supplierPhone" name="supplierPhone" value="<?php echo htmlspecialchars($supplier['supplierPhone']); ?>" required>
        </div>

        <div class="form-group">
          <label for="productSupplied">Products Supplied</label>
          <textarea id="productSupplied" name="productSupplied" required><?php echo htmlspecialchars($supplier['productSupplied']); ?></textarea>
        </div>

        <div class="form-group">
          <label for="productQuantity">Product Quantity</label>
          <input type="number" id="productQuantity" name="productQuantity" min="0" value="<?php echo htmlspecialchars($supplier['productQuantity']); ?>" required>
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
i