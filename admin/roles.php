<?php
// =====================================================
// ✅ DATABASE CONNECTION
// =====================================================
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// =====================================================
// ✅ DELETE LOGIC (Admin cannot be deleted)
// =====================================================
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    // Check if deleting Admin
    $checkAdmin = $conn->prepare("SELECT role_name FROM roles WHERE id = ?");
    $checkAdmin->bind_param("i", $deleteId);
    $checkAdmin->execute();
    $checkAdmin->bind_result($roleToDelete);
    $checkAdmin->fetch();
    $checkAdmin->close();

    if (strtolower($roleToDelete) === 'admin') {
        echo "<script>alert('⚠️ Admin role cannot be deleted!'); window.location.href='roles.php';</script>";
        exit();
    }

    // Delete role
    $deleteQuery = "DELETE FROM roles WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $deleteId);

    if ($stmt->execute()) {
        reorderRoleIDs($conn);
        echo "<script>alert('✅ Role deleted and IDs reordered successfully!'); window.location.href = 'roles.php';</script>";
    } else {
        echo "<p style='color:red;'>Error deleting role: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// =====================================================
// ✅ FUNCTION TO REORDER IDs (Admin=1, Cashier last)
// =====================================================
function reorderRoleIDs($conn) {
    // Disable FK temporarily
    $conn->query("SET FOREIGN_KEY_CHECKS = 0;");

    // Get all roles except Admin and Cashier
    $roles = $conn->query("
        SELECT id, role_name 
        FROM roles 
        WHERE LOWER(role_name) NOT IN ('admin', 'cashier')
        ORDER BY id ASC
    ");

    // Admin always ID 1
    $conn->query("UPDATE roles SET id = 1 WHERE LOWER(role_name) = 'admin'");

    $newId = 2;
    while ($row = $roles->fetch_assoc()) {
        $conn->query("UPDATE roles SET id = $newId WHERE id = " . intval($row['id']));
        $newId++;
    }

    // Cashier always last
    $cashier = $conn->query("SELECT id FROM roles WHERE LOWER(role_name) = 'cashier' LIMIT 1");
    if ($cashier->num_rows > 0) {
        $conn->query("UPDATE roles SET id = $newId WHERE LOWER(role_name) = 'cashier'");
    }

    // Reset AUTO_INCREMENT to next available ID
    $maxID = $conn->query("SELECT MAX(id) AS max_id FROM roles")->fetch_assoc()['max_id'];
    $conn->query("ALTER TABLE roles AUTO_INCREMENT = " . ($maxID + 1));

    $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
}

// =====================================================
// ✅ ADD ROLE LOGIC
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'], $_POST['role_name'])) {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $roleName = trim($_POST['role_name']);

    $insertQuery = "INSERT INTO roles (role_name, username, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sss", $roleName, $username, $password);

    if ($stmt->execute()) {
        reorderRoleIDs($conn);
        echo "<script>alert('✅ New role added and IDs reordered!'); window.location.href = 'roles.php';</script>";
    } else {
        echo "<p style='color:red;'>Error adding role: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// =====================================================
// ✅ FETCH ROLES
// =====================================================
$sql = "SELECT id, role_name, username FROM roles ORDER BY id ASC";
$result = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Role Management</title>
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
    <header>
      <div class="top-bar">
        <div class="logo">
          <img src="../assets/logo.jpg" alt="Logo" />
        </div>
        <div class="search-bar">
          <input type="text" placeholder="Search roles" id="searchInput" />
        </div>
        <div class="user-icons">
          <span class="icon"><i class="fas fa-bell"></i></span>
          <span class="icon"><i class="fas fa-comments"></i></span>
          <a href="profile.html"><span class="icon"><i class="fas fa-user-circle"></i></span></a>
        </div>
      </div>
    </header>

    <h1>Role Management</h1>
    <button class="submit" type="button" onclick="window.location.href='rolesForm.php'">Add Role</button><br>

    <table id="rolesTable">
      <thead>
      <tr>
        <th>ID</th>
        <th>Role Name</th>
        <th>Username</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo str_pad(htmlspecialchars($row['id']), 3, "0", STR_PAD_LEFT); ?></td>
            <td><?php echo htmlspecialchars($row['role_name']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td>
              <a href="edit_role.php?id=<?php echo $row['id']; ?>"><i class="fas fa-edit"></i> Edit</a>
              <?php if (strtolower($row['role_name']) !== 'admin'): ?>
                | <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this role?');"><i class="fas fa-trash-alt"></i> Delete</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4">No roles found.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </main>
</div>
</body>
</html>
