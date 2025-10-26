<?php
// =====================================================
// ✅ DATABASE CONNECTION
// =====================================================
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

// =====================================================
// ✅ FETCH ROLE FOR EDITING
// =====================================================
if (isset($_GET['id'])) {
    $roleId = intval($_GET['id']);

    $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = :id");
    $stmt->bindParam(':id', $roleId, PDO::PARAM_INT);
    $stmt->execute();
    $role = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$role) {
        echo "Role not found.";
        exit;
    }
} else {
    echo "Invalid role ID.";
    exit;
}

// =====================================================
// ✅ HANDLE UPDATE
// =====================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role_name = trim($_POST['role_name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // If password is not empty, hash it
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE roles SET username = :username, password = :password WHERE id = :id";
    } else {
        $updateQuery = "UPDATE roles SET username = :username WHERE id = :id";
    }

    $stmt = $pdo->prepare($updateQuery);
    $stmt->bindParam(':username', $username);
    if (!empty($password)) {
        $stmt->bindParam(':password', $hashedPassword);
    }
    $stmt->bindParam(':id', $roleId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // If not Admin, allow updating role name
        if (strtolower($role['role_name']) !== 'admin' && !empty($role_name)) {
            $updateRoleName = $pdo->prepare("UPDATE roles SET role_name = :role_name WHERE id = :id");
            $updateRoleName->bindParam(':role_name', $role_name);
            $updateRoleName->bindParam(':id', $roleId, PDO::PARAM_INT);
            $updateRoleName->execute();
        }

        echo "<script>alert('✅ Role updated successfully!'); window.location.href = 'roles.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating role.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Role</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/role.css">
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
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.php"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purchase.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.php" class="active"><i class="fas fa-user-cog"></i> Role Management</a></li>
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
            <a href="profile.php">
              <span class="icon"><i class="fas fa-user-circle"></i></span>
            </a>
          </div>
        </div>
      </header>

      <h1>Edit Role</h1>

      <!-- Edit Role Form -->
      <form class="role-form" method="POST" action="edit_role.php?id=<?php echo htmlspecialchars($role['id']); ?>">
        <div class="form-group">
          <label for="role_name">Role Name</label>
          <input type="text" id="role_name" name="role_name" 
                 value="<?php echo htmlspecialchars($role['role_name']); ?>" 
                 <?php echo (strtolower($role['role_name']) === 'admin') ? 'readonly' : ''; ?> required>
        </div>

        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($role['username']); ?>" required>
        </div>

        <div class="form-group">
          <label for="password">New Password</label>
          <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
        </div>

        <div class="form-group">
          <button type="submit">Save Changes</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
