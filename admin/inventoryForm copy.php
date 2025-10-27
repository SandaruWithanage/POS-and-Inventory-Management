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
$supplierQuery = "SELECT id, supplierName, productSupplied FROM suppliers ORDER BY supplierName ASC";
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

    // ✅ Get selected supplier
    $supplierId = !empty($_POST['supplierId']) ? intval($_POST['supplierId']) : null;

    // ✅ Get form data
    $productName = trim($_POST['productName']);
    $productCategory = trim($_POST['productCategory']);
    $productQuantity = !empty($_POST['productQuantity']) ? intval($_POST['productQuantity']) : 0;
    $unitPrice = !empty($_POST['unitPrice']) ? floatval($_POST['unitPrice']) : 0.0;
    $sellingPrice = !empty($_POST['sellingPrice']) ? floatval($_POST['sellingPrice']) : 0.0;
    $stockStatus = trim($_POST['stockStatus']);

    // ✅ Validate that the supplier supplies the chosen product/category
    $validSupplier = false;
    if ($supplierId) {
        $stmt = $conn->prepare("SELECT productSupplied FROM suppliers WHERE id = ?");
        $stmt->bind_param("i", $supplierId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
            $supplied = strtolower($row['productSupplied']);
            if (strpos($supplied, strtolower($productCategory)) !== false || strpos($supplied, strtolower($productName)) !== false) {
                $validSupplier = true;
            }
        }
        $stmt->close();
    }

    if (!$validSupplier) {
        $message = "⚠️ The selected supplier does not supply this product/category.";
    } else {

        // ✅ Generate barcode prefix based on category
        $prefix = '';
        if (preg_match('/(tv|laptop|phone|camera|refrigerator|microwave|speaker|headphone|electronic)/i', $productCategory)) {
            $prefix = 'ELEC';
        } elseif (preg_match('/(sofa|table|chair|bed|cabinet|wardrobe|furniture|desk)/i', $productCategory)) {
            $prefix = 'FURN';
        } else {
            $prefix = 'ITEM';
        }
        $barcodeNo = $prefix . date('Ymd') . sprintf('%04d', rand(1, 9999));

        // ✅ Handle image upload
        $imagePath = '';
        if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxFileSize = 100 * 1024 * 1024;
            $imageTmpName = $_FILES['productImage']['tmp_name'];
            $imageName = $_FILES['productImage']['name'];
            $imageType = $_FILES['productImage']['type'];
            $imageSize = $_FILES['productImage']['size'];

            if (in_array($imageType, $allowedTypes) && $imageSize <= $maxFileSize) {
                $uploadDirectory = 'uploads/';
                if (!file_exists($uploadDirectory)) {
                    mkdir($uploadDirectory, 0777, true);
                }
                $uniqueImageName = uniqid() . '_' . basename($imageName);
                $imagePath = $uploadDirectory . $uniqueImageName;
                if (!move_uploaded_file($imageTmpName, $imagePath)) {
                    $message = "Error uploading the image.";
                }
            } else {
                $message = "Invalid image file type or size.";
            }
        }

        // ✅ Validate all required fields
        if (!empty($productName) && !empty($productCategory) && $productQuantity > 0 && $unitPrice > 0 && $sellingPrice > 0 && !empty($stockStatus)) {

            $totalValue = $productQuantity * $unitPrice;
            $currentTimestamp = date('Y-m-d H:i:s');

            // ✅ Insert record
            $sql = "INSERT INTO inventory 
                    (product_name, barcode_no, category, supplier_id, quantity, unit_price, selling_price, total_value, stock_status, product_image, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param(
                    "sssiiddssss",
                    $productName,
                    $barcodeNo,
                    $productCategory,
                    $supplierId,
                    $productQuantity,
                    $unitPrice,
                    $sellingPrice,
                    $totalValue,
                    $stockStatus,
                    $imagePath,
                    $currentTimestamp,
                    $currentTimestamp
                );

                if ($stmt->execute()) {
                    $message = "✅ New product added successfully with Barcode: " . $barcodeNo;
                    header("Location: inventory.php");
                    exit();
                } else {
                    error_log("Error executing query: " . $stmt->error);
                    $message = "Error executing query, please try again later.";
                }
                $stmt->close();
            } else {
                error_log("Error preparing statement: " . $conn->error);
                $message = "Error preparing query, please try again later.";
            }
        } else {
            $message = "All fields are required and must be valid.";
        }
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
            <a href="profile.html">
              <span class="icon"><i class="fas fa-user-circle"></i></span>
            </a>
          </div>
        </div>
      </header>

      <h1>Add New Inventory Item</h1>

      <!-- FORM -->
      <form class="add-inventory-form" id="addInventoryForm" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
          <label for="productName">Product Name</label>
          <input type="text" id="productName" name="productName" required>
        </div>

        <div class="form-group">
          <label for="productCategory">Category</label>
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

        <div class="form-group">
          <label for="supplierId">Supplier</label>
          <select id="supplierId" name="supplierId" required>
            <option value="">-- Select Supplier --</option>
            <?php foreach ($suppliers as $supplier): ?>
              <option value="<?php echo $supplier['id']; ?>">
                <?php echo htmlspecialchars($supplier['supplierName']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="productQuantity">Quantity</label>
          <input type="number" id="productQuantity" name="productQuantity" min="1" required>
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

        <p class="error-message" id="formErrorMessage"><?php echo $message; ?></p>
      </form>
    </main>
  </div>

  <script>
  document.getElementById('addInventoryForm').addEventListener('submit', function(event) {
      const fileInput = document.getElementById('productImage');
      const file = fileInput.files[0];
      const errorMessage = document.getElementById('formErrorMessage');
      const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
      const maxFileSize = 100 * 1024 * 1024;
      if (file && (!allowedTypes.includes(file.type) || file.size > maxFileSize)) {
          errorMessage.textContent = 'Invalid image file type or size.';
          event.preventDefault();
      }
  });
  </script>
</body>
</html>
