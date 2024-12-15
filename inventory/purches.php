<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project"; // Change this to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $purchaseId = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM purchase WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $purchaseId);
    if ($stmt->execute()) {
        echo "<script>alert('Purchase deleted successfully'); window.location.href = 'purchase.php';</script>";
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
}

// Fetch purchase data from the database
$sql = "SELECT * FROM purchase";
$result = $conn->query($sql);

// Check if query was successful
if ($result === false) {
    echo "Error: " . $conn->error;
    exit;
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Purchase Data</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/supplier.css">
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
        <li><a href="purchase.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
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
            <input type="text" placeholder="Search Purchase Data" id="searchInput">
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

      <h1>Purchase Data</h1>

      <div class="table-header">
        <a href="purchaseForm.php">
          <button id="addPurchaseBtn">Add New Purchase Entry</button>
        </a>
      </div>

      <!-- Purchase Data Table -->
      <table id="purchaseTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Order Name</th>
            <th>Order Date</th>
            <th>Order Status</th>
            <th>Order Value</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . $row['id'] . "</td>";
                  echo "<td>" . $row['order_name'] . "</td>";
                  echo "<td>" . $row['order_date'] . "</td>";
                  echo "<td>" . $row['order_status'] . "</td>";
                  echo "<td>" . $row['order_value'] . "</td>";
                  echo "<td>
                          <a href='edit_purchase.php?id=" . $row['id'] . "'><i class='fas fa-edit'></i> </a> | 
                          <a href='?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete?\");'><i class='fas fa-trash-alt'></i> </a>
                        </td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No records found</td></tr>";
          }
          ?>
        </tbody>
      </table>

      <!-- Pagination Controls -->
      <div class="pagination">
        <button id="prevPage">Previous</button>
        <span id="currentPage">Page 1</span>
        <button id="nextPage">Next</button>
      </div>
    </main>
  </div>

</body>
</html>
