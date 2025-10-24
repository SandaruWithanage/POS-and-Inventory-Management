<?php
// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "final_project"; // Change this to your actual database name

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete logic
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM roles WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        // Reorder IDs after deletion
        $reorderQuery = "SET @id := 0; UPDATE roles SET id = (@id := @id + 1)";
        $conn->query($reorderQuery);
        echo "<script>alert('Role deleted and IDs reordered successfully!'); window.location.href = 'roles.php';</script>";
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch all roles
$sql = "SELECT id, role_name, username FROM roles";
$result = $conn->query($sql);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Role Management</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/role.css">

  <!-- Font Awesome for Icons -->
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
        <li><a href="purches.php"><i class="fas fa-money-bill-wave"></i> Purchases</a></li>
        <li><a href="roles.php" class="active"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <header>
        <div class="top-bar">
          <div class="logo">
            <img src="../assets/logo.jpg" alt="Logo">
          </div>
          <div class="search-bar">
            <input type="text" placeholder="Search roles" id="searchInput">
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

      <h1>Role Management</h1>

      <!-- Roles Table -->
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
                      <td><?php echo htmlspecialchars($row['id']); ?></td>
                      <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                      <td><?php echo htmlspecialchars($row['username']); ?></td>
                      <td>
                          <a href="edit_role.php?id=<?php echo $row['id']; ?>"><i class="fas fa-edit"></i> Edit</a> | 
                          <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this role?');"><i class="fas fa-trash-alt"></i> Delete</a>
                      </td>
                  </tr>
              <?php endwhile; ?>
          <?php else: ?>
              <tr>
                  <td colspan="4">No roles found.</td>
              </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <h2>Update Role</h2>
      <form method="POST">
          <div class="form-group">
              <label for="role_id">Role ID</label>
              <input type="number" id="role_id" name="role_id" required>
          </div>
          <div class="form-group">
              <label for="username">New Username</label>
              <input type="text" id="username" name="username" required>
          </div>
          <div class="form-group">
              <label for="password">New Password</label>
              <input type="password" id="password" name="password" required>
          </div>
          <button id="updateRoleBtn" type="submit">Save Changes</button>
      </form>
    </main>
  </div>
</body>
</html>
