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

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['role_id'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Directly assign the password without hashing

    // Validate input
    if (empty($id) || empty($username) || empty($password)) {
        echo "<p style='color:red;'>All fields are required!</p>";
    } else {
        // Update the role in the database
        $stmt = $conn->prepare("UPDATE roles SET username = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $password, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Role updated successfully!'); window.location.href = 'roles.php';</script>";
        } else {
            echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
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
  <link rel="stylesheet" href="styles/sidebar.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/role.css">

  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.htphpml"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.php"><i class="fas fa-coins"></i> Budget</a></li>
        <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="sales.html"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.html"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.html"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purchase.html"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.html"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
      
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <header>
        <div class="top-bar">
          <div class="logo">
            <img src="assets/logo.jpg" alt="Logo">
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
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                      <td><?php echo htmlspecialchars($row['id']); ?></td>
                      <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                      <td><?php echo htmlspecialchars($row['username']); ?></td>
                  </tr>
              <?php endwhile; ?>
          <?php else: ?>
              <tr>
                  <td colspan="3">No roles found.</td>
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
