<?php
// Database connection
$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "final_project";  

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// ✅ Fetch customer details for editing
if (isset($_GET['id'])) {
    $customerId = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = :id");
    $stmt->bindParam(':id', $customerId, PDO::PARAM_INT);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        echo "Customer not found.";
        exit;
    }
} else {
    echo "Invalid customer ID.";
    exit;
}

// ✅ Update customer details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customerName = $_POST['customerName'];
    $customerEmail = $_POST['customerEmail'];
    $customerPhone = $_POST['customerPhone'];

    $stmt = $pdo->prepare("
        UPDATE customers 
        SET customerName = :customerName, 
            customerEmail = :customerEmail, 
            customerPhone = :customerPhone
        WHERE id = :id
    ");
    $stmt->bindParam(':customerName', $customerName);
    $stmt->bindParam(':customerEmail', $customerEmail);
    $stmt->bindParam(':customerPhone', $customerPhone);
    $stmt->bindParam(':id', $customerId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: customers.php");
        exit;
    } else {
        echo "Error: Could not update customer record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Customer</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/customer.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.php"><i class="fas fa-coins"></i> Budget</a></li>
        <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php" class="active"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.php"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purchases.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
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
            <a href="profile.php">
              <span class="icon"><i class="fas fa-user-circle"></i></span>
            </a>
          </div>
        </div>
      </header>

      <h1>Edit Customer</h1>

      <!-- Edit Customer Form -->
      <form class="add-customer-form" method="POST" action="edit-customer.php?id=<?php echo $customer['id']; ?>">
        <div class="form-group">
          <label for="customerName">Customer Name</label>
          <input type="text" id="customerName" name="customerName" value="<?php echo htmlspecialchars($customer['customerName']); ?>" required>
        </div>

        <div class="form-group">
          <label for="customerEmail">Email Address</label>
          <input type="email" id="customerEmail" name="customerEmail" value="<?php echo htmlspecialchars($customer['customerEmail']); ?>" required>
        </div>

        <div class="form-group">
          <label for="customerPhone">Phone Number</label>
          <input type="text" id="customerPhone" name="customerPhone" value="<?php echo htmlspecialchars($customer['customerPhone']); ?>" required>
        </div>

        <div class="form-group">
          <button type="submit">Save Changes</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
