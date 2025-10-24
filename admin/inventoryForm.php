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
    $barcodeNo = "SHOE" . date('Ymd') . sprintf('%04d', rand(1, 9999));  // Auto-generate barcode
    $productCategory = trim($_POST['productCategory']);
    $productQuantity = !empty($_POST['productQuantity']) ? intval($_POST['productQuantity']) : 0;
    $unitPrice = !empty($_POST['unitPrice']) ? floatval($_POST['unitPrice']) : 0.0;
    $sellingPrice = !empty($_POST['sellingPrice']) ? floatval($_POST['sellingPrice']) : 0.0;
    $stockStatus = trim($_POST['stockStatus']); // Get the stock status from the form

    // Image upload handling
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        // Define allowed file types and size (2MB max)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 100 * 1024 * 1024; // 100MB

        // Get file info
        $imageTmpName = $_FILES['productImage']['tmp_name'];
        $imageName = $_FILES['productImage']['name'];
        $imageType = $_FILES['productImage']['type'];
        $imageSize = $_FILES['productImage']['size'];

        // Validate image file type and size
        if (in_array($imageType, $allowedTypes) && $imageSize <= $maxFileSize) {
            // Generate a unique name for the image file to avoid conflicts
            $imagePath = 'uploads/' . uniqid() . '_' . basename($imageName);
            $uploadDirectory = 'uploads/';

            // Move uploaded file to the server directory
            if (move_uploaded_file($imageTmpName, $uploadDirectory . basename($imagePath))) {
                $imagePath = $uploadDirectory . basename($imagePath);
            } else {
                $message = "Error uploading the image.";
            }
        } else {
            $message = "Invalid image file type or size.";
        }
    } else {
        $imagePath = ''; // No image uploaded
    }

    // Validate required fields
    if (!empty($productName) && !empty($productCategory) && $productQuantity >= 0 && $unitPrice > 0.0 && $sellingPrice > 0.0 && !empty($stockStatus)) {
        // Calculate total value
        $totalValue = $productQuantity * $unitPrice;

        // Get current timestamp for created_at and updated_at
        $currentTimestamp = date('Y-m-d H:i:s');

        // Prepare SQL to insert data into the inventory table
        $sql = "INSERT INTO inventory (product_name, barcode_no, category, quantity, unit_price, selling_price, total_value, stock_status, product_image, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssiiddssss", $productName, $barcodeNo, $productCategory, $productQuantity, $unitPrice, $sellingPrice, $totalValue, $stockStatus, $imagePath, $currentTimestamp, $currentTimestamp);

            // Execute the query and check for success
            if ($stmt->execute()) {
                $message = "New product added successfully with Barcode: " . $barcodeNo;
                // Redirect back to inventory.php after success
                header("Location: inventory.php");
                exit(); // Make sure to exit after redirect
            } else {
                // Log the error for debugging and show a user-friendly message
                error_log("Error executing query: " . $stmt->error);
                $message = "Error executing query, please try again later.";
            }

            $stmt->close();
        } else {
            // Log the error for debugging and show a user-friendly message
            error_log("Error preparing statement: " . $conn->error);
            $message = "Error preparing query, please try again later.";
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
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/inventory.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <ul>
        <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.php"><i class="fas fa-coins"></i> Budget</a></li>
        <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.php"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purchase.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </aside>

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

      <!-- Add Inventory Form -->
      <form class="add-inventory-form" id="addInventoryForm" action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="productName">Product Name</label>
          <input type="text" id="productName" name="productName" required>
        </div>

        <!-- Barcode input is removed since it's auto-generated -->
        <div class="form-group">
          <label for="productCategory">Category</label>
          <select id="productCategory" name="productCategory" required>
            <option value="">-- Select Category --</option>
            <option value="Sneakers">Sneakers</option>
            <option value="Formal Shoes">Formal Shoes</option>
            <option value="Casual Shoes">Casual Shoes</option>
            <option value="Boots">Boots</option>
            <option value="Sandals">Sandals</option>
            <option value="Heels">Heels</option>
            <option value="Flats">Flats</option>
            <option value="Athletic & Sports Shoes">Athletic & Sports Shoes</option>
            <option value="Slippers">Slippers</option>
            <option value="Kids' Shoes">Kids' Shoes</option>
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
    const fileInput = document.getElementById('productImage');
    const file = fileInput.files[0];
    const errorMessage = document.getElementById('formErrorMessage');
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    const maxFileSize = 100 * 1024 * 1024; // 100MB

    // Validate image file type and size
    if (file && (!allowedTypes.includes(file.type) || file.size > maxFileSize)) {
        errorMessage.textContent = 'Invalid image file type or size. Only JPEG, PNG, and GIF files under 100MB are allowed.';
        event.preventDefault();
        return; // Prevent form submission if there's an image error
    }

    // Validate required fields
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
