<?php
// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
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
        // Reorder IDs
        $conn->query("SET @id := 0; UPDATE roles SET id = (@id := @id + 1) ORDER BY id");
        echo "<script>alert('Role deleted and IDs reordered successfully!'); window.location.href = 'roles.php';</script>";
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Add role logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'], $_POST['role_name'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $roleName = $_POST['role_name'];

    $insertQuery = "INSERT INTO roles (role_name, username, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sss", $roleName, $username, $password);
    if ($stmt->execute()) {
        $conn->query("SET @id := 0; UPDATE roles SET id = (@id := @id + 1) ORDER BY id");
        echo "<script>alert('New role added successfully!'); window.location.href = 'roles.php';</script>";
    } else {
        echo "<p style='color:red;'>Error adding role: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch all roles
$sql = "SELECT id, role_name, username FROM roles";
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
    <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
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
    <button class="submit" type="submit">Add Role</button><br>
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
            <td><?php echo str_pad(htmlspecialchars($row['id']), 3, "0", STR_PAD_LEFT); ?></td>
            <td><?php echo htmlspecialchars($row['role_name']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td>
              <a href="edit_role.php?id=<?php echo $row['id']; ?>"><i class="fas fa-edit"></i> Edit</a> |
              <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this role?');"><i class="fas fa-trash-alt"></i> Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4">No roles found.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
    </form>
  </main>
</div>
</body>
</html>
