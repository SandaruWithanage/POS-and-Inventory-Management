<?php
// ================================
// DATABASE CONNECTION
// ================================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ================================
// FETCH SUPPLIERS FOR DROPDOWN
// ================================
$suppliers = [];
$supplierQuery = "SELECT id, supplierName, productSupplied, productQuantity FROM suppliers ORDER BY supplierName ASC";
$result = $conn->query($supplierQuery);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }
}

$message = "";

// ================================
// HANDLE FORM SUBMISSION
// ================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $supplierId = intval($_POST['supplierId']);
    $productCategory = trim($_POST['productCategory']);
    $productName = trim($_POST['productName']);
    $productQuantity = intval($_POST['productQuantity']);
    $unitPrice = floatval($_POST['unitPrice']);
    $sellingPrice = floatval($_POST['sellingPrice']);
    $stockStatus = trim($_POST['stockStatus']);

    // ✅ Barcode prefix based on category
$prefix = preg_match('/(Televisions|Laptops|Phones|Cameras|Refrigerators|Microwaves|Speakers|Headphones)/i', $productCategory)
    ? 'ELEC'
    : (preg_match('/(Sofas|Beds|Tables|Chairs|Cabinets|Wardrobes|Desks)/i', $productCategory) ? 'FURN' : 'ITEM');

// ✅ Generate truly unique barcode (prefix + date + unique ID)
$barcodeNo = $prefix . date('YmdHis') . strtoupper(substr(uniqid(), -4));


    // ✅ Handle image upload
    $imagePath = '';
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $uploadDirectory = 'uploads/';
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }
        $imageName = uniqid() . '_' . basename($_FILES['productImage']['name']);
        move_uploaded_file($_FILES['productImage']['tmp_name'], $uploadDirectory . $imageName);
        $imagePath = $uploadDirectory . $imageName;
    }

    // ✅ Validate and insert
    if (!empty($productName) && !empty($productCategory) && $productQuantity > 0 && $unitPrice > 0 && $sellingPrice > 0 && !empty($stockStatus)) {
        $totalValue = $productQuantity * $unitPrice;
        $timestamp = date('Y-m-d H:i:s');

       $sql = "INSERT INTO inventory 
        (product_name, barcode_no, category, supplier_id, quantity, unit_price, selling_price, total_value, stock_status, product_image, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssiidddssss",
    $productName,      // s
    $barcodeNo,        // s
    $productCategory,  // s
    $supplierId,       // i
    $productQuantity,  // i
    $unitPrice,        // d
    $sellingPrice,     // d
    $totalValue,       // d
    $stockStatus,      // s
    $imagePath,        // s
    $timestamp,        // s
    $timestamp         // s
);

        $stmt->execute();
        $stmt->close();
        header("Location: inventory.php");
        exit();
    } else {
        $message = "⚠️ Please fill all required fields correctly.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Inventory</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/inventory.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.php" class="active"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.php"><i class="fas fa-coins"></i> Budget</a></li>
        <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
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
            <a href="profile.html"><span class="icon"><i class="fas fa-user-circle"></i></span></a>
          </div>
        </div>
      </header>

      <h1>Add New Inventory Item</h1>

      <!-- FORM -->
      <form class="add-inventory-form" id="addInventoryForm" method="POST" enctype="multipart/form-data">

        <!-- Supplier -->
        <div class="form-group">
          <label for="supplierId">Supplier</label>
          <select id="supplierId" name="supplierId" required>
            <option value="">-- Select Supplier --</option>
            <?php foreach ($suppliers as $supplier): ?>
              <option 
                value="<?= $supplier['id']; ?>"
                data-product="<?= htmlspecialchars($supplier['productSupplied']); ?>"
                data-quantity="<?= htmlspecialchars($supplier['productQuantity']); ?>">
                <?= htmlspecialchars($supplier['supplierName']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Category -->
        <div class="form-group">
          <label for="productCategory">Product Category</label>
          <select id="productCategory" name="productCategory" required>
            <option value="">-- Select Category --</option>
            <optgroup label="📺 Electronics">
              <option value="Televisions">Televisions</option>
              <option value="Laptops">Laptops</option>
              <option value="Mobile Phones">Mobile Phones</option>
              <option value="Refrigerators">Refrigerators</option>
              <option value="Washing Machines">Washing Machines</option>
              <option value="Microwaves">Microwaves</option>
              <option value="Speakers">Speakers</option>
              <option value="Headphones">Headphones</option>
              <option value="Cameras">Cameras</option>
            </optgroup>
            <optgroup label="🪑 Furniture">
              <option value="Sofas">Sofas</option>
              <option value="Beds">Beds</option>
              <option value="Dining Tables">Dining Tables</option>
              <option value="Chairs">Chairs</option>
              <option value="Cabinets">Cabinets</option>
              <option value="Wardrobes">Wardrobes</option>
              <option value="Office Desks">Office Desks</option>
            </optgroup>
          </select>
        </div>

        <!-- Product Name -->
        <div class="form-group">
          <label for="productName">Product Name</label>
          <input type="text" id="productName" name="productName" readonly required>
        </div>

        <!-- Quantity -->
        <div class="form-group">
          <label for="productQuantity">Quantity</label>
          <input type="number" id="productQuantity" name="productQuantity" readonly required>
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
          <label for="productImage">Product Image</label>
          <input type="file" id="productImage" name="productImage" accept="image/*">
        </div>

        <div class="form-group">
          <button type="submit">Add Product</button>
        </div>

        <p class="error-message" id="formErrorMessage"><?= $message; ?></p>
      </form>
    </main>
  </div>

  <script>
  // When supplier changes, auto-fill product name, quantity, and select category
  document.getElementById('supplierId').addEventListener('change', function() {
      const selected = this.options[this.selectedIndex];
      const productName = selected.getAttribute('data-product') || '';
      const quantity = selected.getAttribute('data-quantity') || 0;

      document.getElementById('productName').value = productName;
      document.getElementById('productQuantity').value = quantity;

      // Automatically set category dropdown if product matches
      const categorySelect = document.getElementById('productCategory');
      let matched = false;
      for (let i = 0; i < categorySelect.options.length; i++) {
          const opt = categorySelect.options[i];
          if (productName.toLowerCase().includes(opt.value.toLowerCase())) {
              categorySelect.value = opt.value;
              matched = true;
              break;
          }
      }
      if (!matched) categorySelect.value = '';
  });
  </script>
</body>
</html>
