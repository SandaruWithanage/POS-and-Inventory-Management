<?php
// Database connection settings
$servername = "localhost";
$username = "root";  // Change this to your database username
$password = "";      // Change this to your database password
$dbname = "final_project";  // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and use trim() to remove any whitespace around inputs
    $productName = trim($_POST['productName']);
    $barcodeNo = trim($_POST['barcodeNo']);
    $productCategory = trim($_POST['productCategory']);
    $productQuantity = !empty($_POST['productQuantity']) ? intval($_POST['productQuantity']) : 0;
    $unitPrice = !empty($_POST['unitPrice']) ? floatval($_POST['unitPrice']) : 0.0;
    $sellingPrice = !empty($_POST['sellingPrice']) ? floatval($_POST['sellingPrice']) : 0.0;
    $stockStatus = trim($_POST['stockStatus']); // Get the stock status from the form

    // Validate required fields
    if (!empty($productName) && !empty($barcodeNo) && !empty($productCategory) && $productQuantity >= 0 && $unitPrice > 0.0 && $sellingPrice > 0.0 && !empty($stockStatus)) {
        
        // Prepare SQL to insert data into the products table, including stock status and selling price
        $sql = "INSERT INTO products (product_name, barcode_no, category, quantity, unit_price, selling_price, stock_status)
                VALUES ('$productName', '$barcodeNo', '$productCategory', '$productQuantity', '$unitPrice', '$sellingPrice', '$stockStatus')";

        // Execute the SQL statement directly without using bind_param()
        if ($conn->query($sql) === TRUE) {
            $message = "New product added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "All fields are required.";
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Inventory</title>
  <link rel="stylesheet" href="styles/sidebar.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/inventory.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.html"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.html"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.html"><i class="fas fa-coins"></i> Budget</a></li>
        <li><a href="costs.html"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.html"><i class="fas fa-file-invoice-dollar"></i> Income </a></li>
        <li><a href="sales.html"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.html"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.html"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.html"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purches.html"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.html"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <button id="logout-btn" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
    </aside>

    <main class="main-content">
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

      <h1>Add New Inventory Item</h1>

      <!-- Add Inventory Form -->
      <form class="add-inventory-form" id="addInventoryForm" action="" method="POST">
        <div class="form-group">
          <label for="productName">Product Name</label>
          <input type="text" id="productName" name="productName" required>
        </div>

        <div class="form-group">
          <label for="barcodeNo">Barcode No:</label>
          <input type="text" id="barcodeNo" name="barcodeNo" required>
        </div>

        <div class="form-group">
          <label for="productCategory">Category</label>
          <select id="productCategory" name="productCategory" required>
            <option value=""></option>
            <option value="electronics">Electronics</option>
            <option value="furniture">Furniture</option>
          </select>
        </div>

        <div class="form-group">
          <label for="productQuantity">Quantity</label>
          <input type="number" id="productQuantity" name="productQuantity" min="0" required>
        </div>

        <div class="form-group">
          <label for="unitPrice">Unit Price</label>
          <input type="number" id="unitPrice" name="unitPrice" min="0.01" step="0.01" required>
        </div>

        <div class="form-group">
          <label for="sellingPrice">Selling Price</label>
          <input type="number" id="sellingPrice" name="sellingPrice" min="0.01" step="0.01" required>
        </div>

        <div class="form-group">
          <label for="stockStatus">Stock Status</label>
          <select id="stockStatus" name="stockStatus" required>
            <option value="In Stock">In Stock</option>
            <option value="Out of Stock">Out of Stock</option>
          </select>
        </div>

        <div class="form-group">
          <button type="submit">Add Product</button>
        </div>

        <p class="error-message" id="formErrorMessage"><?php echo $message; ?></p>
      </form>

    </main>
  </div>

  <script>
    document.getElementById('addInventoryForm').addEventListener('submit', function(event) {
      const errorMessage = document.getElementById('formErrorMessage');

      if (!document.getElementById('productName').value ||
          !document.getElementById('productCategory').value ||
          !document.getElementById('productQuantity').value ||
          !document.getElementById('unitPrice').value ||
          !document.getElementById('sellingPrice').value ||
          !document.getElementById('stockStatus').value) {
        errorMessage.textContent = 'All fields are required.';
        event.preventDefault();
      }
    });
  </script>
</body>
</html>
