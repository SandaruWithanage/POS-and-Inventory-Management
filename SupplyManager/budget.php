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

// Pagination setup
$records_per_page = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Get current page
$offset = ($page - 1) * $records_per_page;

// Handle delete action
if (isset($_GET['delete_id'])) {
    $budgetId = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM budget WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $budgetId);
    if ($stmt->execute()) {
        echo "<script>alert('Budget deleted successfully'); window.location.href = 'budget.php';</script>";
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
}

// Fetch budget data with pagination
$sql = "SELECT * FROM budget LIMIT $offset, $records_per_page";
$result = $conn->query($sql);

// Get the total number of records for pagination
$total_records_query = "SELECT COUNT(*) FROM budget";
$total_records_result = $conn->query($total_records_query);
$total_records = $total_records_result->fetch_row()[0];
$total_pages = ceil($total_records / $records_per_page);

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
  <title>Budget Data</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/budgets.css">
  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="SupplyManager/inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="SupplyManager/suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="SupplyManager/budget.php"><i class="fas fa-money-bill"></i> Budget</a></li>
      <li><a href="SupplyManager/sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="SupplyManager/orders.php" id="ordersMenuItem"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="SupplyManager/shipment.php"><i class="fas fa-shipping-fast"></i> Shipment & Purchase</a></li>
      </ul>
      <button id="logout-btn" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
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
            <input type="text" placeholder="Search Budget Data" id="searchInput">
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

      <h1>Budget Data</h1>

      <div class="table-header">
        <a href="budgetForm.php">
          <button id="addBudgetBtn">Add New Budget Entry</button>
        </a>
      </div>

      <!-- Budget Data Table -->
      <table id="budgetTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Start Date</th>
            <th>Amount</th>
            <th>Budget Rate</th>
            <th>End Date</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . $row['id'] . "</td>";
                  echo "<td>" . $row['start_date'] . "</td>";
                  echo "<td>" . $row['amount'] . "</td>";
                  echo "<td>" . $row['budget_rate'] . "</td>";
                  echo "<td>" . $row['end_date'] . "</td>";
                  echo "<td>" . $row['created_at'] . "</td>";
                  echo "<td>
                          <a href='edit-budget.php?id=" . $row['id'] . "'><i class='fas fa-edit'></i> </a> | 
                          <a href='?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete?\");'><i class='fas fa-trash-alt'></i> </a>
                        </td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='7'>No records found</td></tr>";
          }
          ?>
        </tbody>
      </table>

      <!-- Pagination Controls -->
      <div class="pagination">
        <?php if ($page > 1): ?>
          <button id="prevPage">
            <a href="budget.php?page=<?php echo $page - 1; ?>">Previous</a>
          </button>
        <?php endif; ?>

        <span id="currentPage">Page <?php echo $page; ?></span>

        <?php if ($page < $total_pages): ?>
          <button id="nextPage">
            <a href="budget.php?page=<?php echo $page + 1; ?>">Next</a>
          </button>
        <?php endif; ?>
      </div>
    </main>
  </div>

</body>
</html>
