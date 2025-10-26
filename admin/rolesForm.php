<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project"; // Use your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $roleName = trim($_POST['role_name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $description = trim($_POST['description']);

    // Validate inputs
    if (empty($roleName) || empty($username) || empty($password)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {
        // Hash the password securely
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Prepare and bind SQL
        $stmt = $conn->prepare("INSERT INTO roles (role_name, username, password, description) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
        } else {
            $stmt->bind_param("ssss", $roleName, $username, $hashedPassword, $description);

            // Execute query
            if ($stmt->execute()) {
                echo "<script>alert('Role added successfully!'); window.location.href = 'roles.php';</script>";
            } else {
                echo "<script>alert('Error: " . $stmt->error . "');</script>";
            }

            $stmt->close();
        }
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add New Role</title>
  <link rel="stylesheet" href="../styles/sidebar.css" />
  <link rel="stylesheet" href="../styles/topbar.css" />
  <link rel="stylesheet" href="../styles/role.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
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
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.php"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purchase.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.php" class="active"><i class="fas fa-user-cog"></i> Role Management</a></li>
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

      <h1>Add New Role</h1>

      <!-- Add Role Form -->
      <form class="add-sale-form" id="addRoleForm" method="POST">
        <div class="form-group">
          <label for="role_name">Role Name</label>
          <select id="role_name" name="role_name" required>
            <option value="">-- Select Role --</option>
            <!--<option value="Admin">Admin</option>-->
            <option value="FinanceManager">Finance Manager</option>
            <option value="ProcurementManager">Procurement Manager</option>
            <option value="InventoryManager">Inventory Manager</option>
            <option value="Cashier">Cashier</option>
          </select>
        </div>

        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Enter username" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter password" required>
        </div>

        <div class="form-group">
          <label for="description">Description (optional)</label>
          <textarea id="description" name="description" rows="2" placeholder="Short description..."></textarea>
        </div>

        <div class="form-group">
          <button type="submit">Add Role</button>
        </div>

        <a href="roles.php" style="color:#e74c3c; text-decoration:none; font-weight:bold;">
          <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
      </form>
    </main>
  </div>
</body>
</html>
