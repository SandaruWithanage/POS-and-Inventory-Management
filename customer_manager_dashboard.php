<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "final_project";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch stats
$totalProductsQuery = "SELECT COUNT(id) AS total_products FROM inventory";
$totalCategoriesQuery = "SELECT COUNT(DISTINCT category) AS total_categories FROM inventory";
$totalIncomeQuery = "SELECT SUM(selling_price - unit_price) AS total_income FROM inventory";
$outOfStockQuery = "SELECT product_name, category, quantity FROM inventory WHERE stock_status = 'Out of Stock'";

// Execute queries
$totalProductsResult = $conn->query($totalProductsQuery)->fetch_assoc();
$totalCategoriesResult = $conn->query($totalCategoriesQuery)->fetch_assoc();
$totalIncomeResult = $conn->query($totalIncomeQuery)->fetch_assoc();
$outOfStockResult = $conn->query($outOfStockQuery);

// Assign results
$totalProducts = $totalProductsResult['total_products'] ?? 0;
$totalCategories = $totalCategoriesResult['total_categories'] ?? 0;
$totalIncome = $totalIncomeResult['total_income'] ?? 0;

// Close the connection for security reasons
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory Manager Dashboard</title>
  <link rel="stylesheet" href="styles/dashboard.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/sidebar.css">

  <style>
    .out-of-stock-table {
    margin: 20px;
    width: 100%;
    border-collapse: collapse;
  }

  .out-of-stock-table th,
  .out-of-stock-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
  }

  .out-of-stock-table th {
    background-color: #f4f4f4;
  }
  </style>

  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
    <ul>
        
        <li><a href="finance/customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        
      </ul>
      <button id="logout-btn" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header>
        <div class="top-bar">
          <div class="logo">
            <img src="assets/logo.jpg" alt="Logo" style="height: 50px;">
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
      </header>

      <!-- Dashboard Stats -->
      <section class="dashboard-cards">
        <div class="card">
          <i class="fas fa-box card-icon"></i>
          <h3>Total Products</h3>
          <p><?= $totalProducts; ?></p>
        </div>
        <div class="card">
          <i class="fas fa-th card-icon"></i>
          <h3>Total Categories</h3>
          <p><?= $totalCategories; ?></p>
        </div>
        <div class="card">
          <i class="fas fa-dollar-sign card-icon"></i>
          <h3>Predicted Income</h3>
          <p>$<?= number_format($totalIncome, 2); ?></p>
        </div>
      </section>

      <!-- Out of Stock Products -->
      <section class="out-of-stock">
        <h2>Out of Stock Products</h2>
        <table class="out-of-stock-table">
          <thead>
            <tr>
              <th>Product Name</th>
              <th>Category</th>
              <th>Quantity</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($outOfStockResult->num_rows > 0): ?>
              <?php while ($product = $outOfStockResult->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($product['product_name']); ?></td>
                  <td><?= htmlspecialchars($product['category']); ?></td>
                  <td class="status-out"><?= htmlspecialchars($product['quantity']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="3">No out-of-stock products found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>
</html>
