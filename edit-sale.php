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

// Fetch sale details for editing
if (isset($_GET['id'])) {
    $saleId = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM sales WHERE id = ?");
    $stmt->bind_param("i", $saleId);
    $stmt->execute();
    $result = $stmt->get_result();
    $sale = $result->fetch_assoc();

    if (!$sale) {
        echo "<p style='color:red;'>Sale record not found.</p>";
        exit;
    }
} else {
    echo "<p style='color:red;'>Invalid request. No ID provided.</p>";
    exit;
}

// Update sale functionality
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = $_POST['productName'];
    $quantity = $_POST['quantity'];
    $unitPrice = $_POST['unitPrice'];
    $sellingPrice = $_POST['sellingPrice'];
    $totalAmount = $_POST['totalAmount'];
    $salesDate = $_POST['salesDate'];

    // Validate input
    if (empty($productName) || empty($quantity) || empty($unitPrice) || empty($sellingPrice) || empty($totalAmount) || empty($salesDate)) {
        echo "<p style='color:red;'>All fields are required!</p>";
    } else {
        // Update the sale record in the database
        $stmt = $conn->prepare("UPDATE sales SET product_name = ?, quantity = ?, unit_price = ?, selling_price = ?, total_amount = ?, sales_date = ? WHERE id = ?");
        $stmt->bind_param("siidssi", $productName, $quantity, $unitPrice, $sellingPrice, $totalAmount, $salesDate, $saleId);

        if ($stmt->execute()) {
            // Redirect to sales.php after successful update
            header("Location: sales.php");
            exit;
        } else {
            echo "<p style='color:red;'>Error: Could not update sale record.</p>";
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
  <title>Edit Sale</title>
  <link rel="stylesheet" href="styles/sidebar.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/sale.css">

  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.html"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.html"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.html"><i class="fas fa-coins"></i>Budget</a></li>
        <li><a href="costs.html"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.html"><i class="fas fa-file-invoice-dollar"></i> Income </a></li>
        <li><a href="sales.html"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.html"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.html"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.html"><i class="fas fa-shipping-fast"></i> Shipment </a></li>
        <li><a href="purches.html"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.html"><i class="fas fa-user-cog"></i> Role Management</a></li>
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

      <h1>Edit Sale</h1>

      <!-- Edit Sale Form -->
      <form class="edit-sale-form" id="editSaleForm" method="POST">

        <div class="form-group">
          <label for="productName">Product Name</label>
          <input type="text" id="productName" name="productName" value="<?php echo htmlspecialchars($sale['product_name']); ?>" required>
        </div>

        <div class="form-group">
          <label for="quantity">Quantity</label>
          <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($sale['quantity']); ?>" required>
        </div>

        <div class="form-group">
          <label for="unitPrice">Unit Price (LKR)</label>
          <input type="number" step="0.01" id="unitPrice" name="unitPrice" value="<?php echo htmlspecialchars($sale['unit_price']); ?>" required>
        </div>

        <div class="form-group">
          <label for="sellingPrice">Selling Price (LKR)</label>
          <input type="number" step="0.01" id="sellingPrice" name="sellingPrice" value="<?php echo htmlspecialchars($sale['selling_price']); ?>" required>
        </div>

        <div class="form-group">
          <label for="totalAmount">Total Amount (LKR)</label>
          <input type="number" step="0.01" id="totalAmount" name="totalAmount" value="<?php echo htmlspecialchars($sale['total_amount']); ?>" required>
        </div>

        <div class="form-group">
          <label for="salesDate">Sales Date</label>
          <input type="date" id="salesDate" name="salesDate" value="<?php echo htmlspecialchars($sale['sales_date']); ?>" required>
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
