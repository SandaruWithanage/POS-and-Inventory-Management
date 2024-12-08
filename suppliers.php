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

// Handle record deletion
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM suppliers WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $deleteId);
    
    if ($stmt->execute()) {
        echo "<script>alert('Supplier record deleted successfully'); window.location.href = 'suppliers.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch supplier data
$sql = "SELECT * FROM suppliers"; // Adjust the query for your table and fields
$result = $conn->query($sql);

$supplierData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $supplierData[] = $row;
    }
} else {
    $supplierData = []; // No data found
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suppliers</title>
  <link rel="stylesheet" href="styles/sidebar.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/supplier.css">

  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.html"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.html"><i class="fas fa-coins"></i> Budget</a></li>
        <li><a href="costs.html"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.html"><i class="fas fa-file-invoice-dollar"></i> Income </a></li>
        <li><a href="sales.html"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.html"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.html"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.html"><i class="fas fa-shipping-fast"></i> Shipment </a></li>
        <li><a href="purches.html"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.html"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <header>
        <div class="top-bar">
          <!-- Add logo image -->
          <div class="logo">
            <img src="assets/logo.jpg" alt="Logo">
          </div>
          <div class="search-bar">
            <input type="text" placeholder="Type for search" id="searchInput">
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

      <h1>Suppliers</h1>

      <div class="table-header">
        <a href="suppliersForm.php">
          <button id="addSupplierBtn">Add New Supplier</button>
        </a>
      </div>

      <!-- Suppliers Table -->
      <table id="suppliersTable">
        <thead>
          <tr>
            <th>Supplier ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Product Supplied</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
    <!-- Dynamic supplier data will be inserted here -->
    <?php if (count($supplierData) > 0): ?>
        <?php foreach ($supplierData as $supplier): ?>
            <tr>
                <td><?php echo htmlspecialchars($supplier['id']); ?></td>
                <td><?php echo htmlspecialchars($supplier['supplierName']); ?></td>
                <td><?php echo htmlspecialchars($supplier['supplierEmail']); ?></td>
                <td><?php echo htmlspecialchars($supplier['supplierPhone']); ?></td>
                <td><?php echo htmlspecialchars($supplier['productSupplied']); ?></td>
                <td>
                    <!-- Edit Button -->
                    <a href="editSupplier.php?id=<?php echo $supplier['id']; ?>" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <!-- Delete Button -->
                    <a href="suppliers.php?delete_id=<?php echo $supplier['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');" title="Delete">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No supplier records found.</td>
        </tr>
    <?php endif; ?>
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
