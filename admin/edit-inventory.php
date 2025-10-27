<?php
// ================================
// DATABASE CONNECTION
// ================================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// ================================
// FETCH INVENTORY DETAILS
// ================================
if (!isset($_GET['id'])) {
    echo "Invalid request. No ID provided.";
    exit;
}

$inventoryId = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = :id");
$stmt->bindParam(':id', $inventoryId, PDO::PARAM_INT);
$stmt->execute();
$inventory = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inventory) {
    echo "Inventory record not found.";
    exit;
}

// ================================
// FETCH SUPPLIERS
// ================================
$suppliers = [];
$supplierQuery = $pdo->query("SELECT id, supplierName FROM suppliers ORDER BY supplierName ASC");
$suppliers = $supplierQuery->fetchAll(PDO::FETCH_ASSOC);

// ================================
// UPDATE INVENTORY FUNCTIONALITY
// ================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplierId = $_POST['supplier_id'] ?? null;
    $productName = $_POST['product_name'] ?? null;
    $productCategory = $_POST['category'] ?? null;
    $quantity = $_POST['quantity'] ?? null;
    $unitPrice = $_POST['unit_price'] ?? null;
    $sellingPrice = $_POST['selling_price'] ?? null;
    $stockStatus = $_POST['stock_status'] ?? "In Stock";

    $totalValue = $quantity * $unitPrice;
    $timestamp = date('Y-m-d H:i:s');

    // ✅ Handle image update (optional)
    $imagePath = $inventory['product_image'];
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        $imageName = uniqid() . '_' . basename($_FILES['product_image']['name']);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadDir . $imageName);
        $imagePath = $uploadDir . $imageName;
    }

    if ($productName && $productCategory && $quantity > 0 && $unitPrice > 0 && $sellingPrice > 0) {
        $stmt = $pdo->prepare("
            UPDATE inventory 
            SET supplier_id = :supplier_id,
                product_name = :product_name,
                category = :category,
                quantity = :quantity,
                unit_price = :unit_price,
                selling_price = :selling_price,
                total_value = :total_value,
                stock_status = :stock_status,
                product_image = :product_image,
                updated_at = :updated_at
            WHERE id = :id
        ");
        $stmt->execute([
            ':supplier_id' => $supplierId,
            ':product_name' => $productName,
            ':category' => $productCategory,
            ':quantity' => $quantity,
            ':unit_price' => $unitPrice,
            ':selling_price' => $sellingPrice,
            ':total_value' => $totalValue,
            ':stock_status' => $stockStatus,
            ':product_image' => $imagePath,
            ':updated_at' => $timestamp,
            ':id' => $inventoryId
        ]);

        header("Location: inventory.php");
        exit;
    } else {
        echo "⚠️ Please fill all required fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Inventory</title>
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
        </div>
      </header>

      <h1>Edit Inventory Item</h1>

      <!-- Edit Inventory Form -->
      <form method="POST" enctype="multipart/form-data">
        <!-- Supplier -->
        <div class="form-group">
          <label for="supplier_id">Supplier</label>
          <select id="supplier_id" name="supplier_id" required>
            <option value="">-- Select Supplier --</option>
            <?php foreach ($suppliers as $supplier): ?>
              <option value="<?= htmlspecialchars($supplier['id']); ?>" 
                <?= $supplier['id'] == $inventory['supplier_id'] ? 'selected' : ''; ?>>
                <?= htmlspecialchars($supplier['supplierName']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Category -->
        <div class="form-group">
          <label for="category">Category</label>
          <select id="category" name="category" required>
            <option value="">-- Select Category --</option>
            <optgroup label="📺 Electronics">
              <option value="Televisions" <?= $inventory['category']=='Televisions'?'selected':''; ?>>Televisions</option>
              <option value="Laptops" <?= $inventory['category']=='Laptops'?'selected':''; ?>>Laptops</option>
              <option value="Mobile Phones" <?= $inventory['category']=='Mobile Phones'?'selected':''; ?>>Mobile Phones</option>
              <option value="Refrigerators" <?= $inventory['category']=='Refrigerators'?'selected':''; ?>>Refrigerators</option>
              <option value="Washing Machines" <?= $inventory['category']=='Washing Machines'?'selected':''; ?>>Washing Machines</option>
              <option value="Microwaves" <?= $inventory['category']=='Microwaves'?'selected':''; ?>>Microwaves</option>
              <option value="Speakers" <?= $inventory['category']=='Speakers'?'selected':''; ?>>Speakers</option>
              <option value="Headphones" <?= $inventory['category']=='Headphones'?'selected':''; ?>>Headphones</option>
              <option value="Cameras" <?= $inventory['category']=='Cameras'?'selected':''; ?>>Cameras</option>
            </optgroup>
            <optgroup label="🪑 Furniture">
              <option value="Sofas" <?= $inventory['category']=='Sofas'?'selected':''; ?>>Sofas</option>
              <option value="Beds" <?= $inventory['category']=='Beds'?'selected':''; ?>>Beds</option>
              <option value="Dining Tables" <?= $inventory['category']=='Dining Tables'?'selected':''; ?>>Dining Tables</option>
              <option value="Chairs" <?= $inventory['category']=='Chairs'?'selected':''; ?>>Chairs</option>
              <option value="Cabinets" <?= $inventory['category']=='Cabinets'?'selected':''; ?>>Cabinets</option>
              <option value="Wardrobes" <?= $inventory['category']=='Wardrobes'?'selected':''; ?>>Wardrobes</option>
              <option value="Office Desks" <?= $inventory['category']=='Office Desks'?'selected':''; ?>>Office Desks</option>
            </optgroup>
          </select>
        </div>

        <!-- Product -->
        <div class="form-group">
          <label for="product_name">Product Name</label>
          <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($inventory['product_name']); ?>" required>
        </div>

        <!-- Quantity -->
        <div class="form-group">
          <label for="quantity">Quantity</label>
          <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($inventory['quantity']); ?>" required>
        </div>

        <!-- Unit Price -->
        <div class="form-group">
          <label for="unit_price">Unit Price</label>
          <input type="number" id="unit_price" name="unit_price" value="<?= htmlspecialchars($inventory['unit_price']); ?>" step="0.01" required>
        </div>

        <!-- Selling Price -->
        <div class="form-group">
          <label for="selling_price">Selling Price</label>
          <input type="number" id="selling_price" name="selling_price" value="<?= htmlspecialchars($inventory['selling_price']); ?>" step="0.01" required>
        </div>

        <!-- Stock Status -->
        <div class="form-group">
          <label for="stock_status">Stock Status</label>
          <select id="stock_status" name="stock_status" required>
            <option value="In Stock" <?= $inventory['stock_status']=='In Stock'?'selected':''; ?>>In Stock</option>
            <option value="Out of Stock" <?= $inventory['stock_status']=='Out of Stock'?'selected':''; ?>>Out of Stock</option>
          </select>
        </div>

        <!-- Product Image -->
        <div class="form-group">
          <label for="product_image">Product Image</label>
          <?php if (!empty($inventory['product_image'])): ?>
            <img src="<?= htmlspecialchars($inventory['product_image']); ?>" alt="Product Image" width="100" style="display:block;margin-bottom:10px;">
          <?php endif; ?>
          <input type="file" id="product_image" name="product_image" accept="image/*">
        </div>

        <div class="form-group">
          <button type="submit">Save Changes</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
