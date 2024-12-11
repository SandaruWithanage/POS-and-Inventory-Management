<?php
// database connection
$servername = "localhost";
$username = "root";  // Change this to your database username
$password = "";      // Change this to your database password
$dbname = "final_project";  // Replace with your actual database name

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Pagination setup
$records_per_page = 10; // Number of records to display per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
$offset = ($page - 1) * $records_per_page;

// Fetch data from the 'income' table with pagination
$stmt = $pdo->prepare("SELECT * FROM income LIMIT :offset, :records_per_page");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$income_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of records for pagination
$stmt = $pdo->query("SELECT COUNT(*) FROM income");
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

// Delete record functionality
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Debugging: Check if delete_id is being passed correctly
    // echo "Delete ID: " . $delete_id; // Uncomment for debugging

    // Prepare the delete statement
    $stmt = $pdo->prepare("DELETE FROM income WHERE id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect back to the page after deletion
    header("Location: income-costs.php");
    exit; // Make sure the script stops after the redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Income</title>
  <link rel="stylesheet" href="styles/sidebar.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/income.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.html"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.html"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.html"><i class="fas fa-coins"></i> Budget</a></li>
        <li><a href="costs.html"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="sales.html"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.html"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.html"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.html"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
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

      <h1>Income</h1>

      <div class="table-header">
        <a href="income-costsForm.php">
          <button id="addIncomeBtn">Add New Income</button>
        </a>
      </div>
      
      <!-- Income Table -->
      <table id="incomeTable">
        <thead>
          <tr>
            <th>Income ID</th>
            <th>Rate</th>
            <th>Amount (LKR)</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($income_records as $record): ?>
            <tr>
              <td><?php echo htmlspecialchars($record['id']); ?></td>
              <td><?php echo htmlspecialchars($record['income_rate']); ?></td>
              <td><?php echo htmlspecialchars($record['income_amount']); ?></td>
              <td>
                <!-- Edit Button with Font Awesome -->
                <a href="editIncome.php?id=<?php echo $record['id']; ?>" class="edit-btn"><i class="fas fa-edit"></i></a>
                
                <!-- Delete Button with Font Awesome -->
                <a href="income-costs.php?delete_id=<?php echo urlencode($record['id']); ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this record?');"><i class="fas fa-trash-alt"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Pagination Controls -->
      <div class="pagination">
        <button id="prevPage" <?php if ($page == 1) echo 'disabled'; ?>><a href="income.php?page=<?php echo $page - 1; ?>">Previous</a></button>
        <span id="currentPage">Page <?php echo $page; ?></span>
        <button id="nextPage" <?php if ($page == $total_pages) echo 'disabled'; ?>><a href="income-costs.php?page=<?php echo $page + 1; ?>">Next</a></button>
      </div>
    </main>
  </div>

</body>
</html>
