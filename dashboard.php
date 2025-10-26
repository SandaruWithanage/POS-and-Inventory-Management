<?php
session_start();

// --- Auth: only admin allowed
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

// central DB connection
require_once __DIR__ . '/config/db_connect.php';

// --- KPIs and queries ---
$totalProducts = 0;
$totalCategories = 0;
$totalIncome = 0.0;
$totalSales = 0;
$activeCustomers = 0;
$outOfStock = [];
$recentSales = [];
$topProducts = [];

// ✅ total products
$q = "SELECT COUNT(id) AS total_products FROM inventory";
if ($res = $conn->query($q)) {
    $totalProducts = (int)$res->fetch_assoc()['total_products'];
} else {
    die("Error (total products): " . $conn->error);
}

// ✅ total categories
$q = "SELECT COUNT(DISTINCT category) AS total_categories FROM inventory";
if ($res = $conn->query($q)) {
    $totalCategories = (int)$res->fetch_assoc()['total_categories'];
} else {
    die("Error (total categories): " . $conn->error);
}

// ✅ total sales + revenue
$q = "SELECT COUNT(id) AS total_sales, IFNULL(SUM(total_amount),0) AS total_revenue FROM sales";
if ($res = $conn->query($q)) {
    $row = $res->fetch_assoc();
    $totalSales = (int)$row['total_sales'];
    $totalIncome = (float)$row['total_revenue'];
} else {
    die("Error (sales summary): " . $conn->error);
}

// ✅ active customers
$q = "SELECT COUNT(DISTINCT customerName) AS cnt FROM customers";
if ($res = $conn->query($q)) {
    $activeCustomers = (int)$res->fetch_assoc()['cnt'];
} else {
    die("Error (customers): " . $conn->error);
}

// ✅ recent sales
$q = "SELECT id, product_name, quantity, total_amount, sales_date 
      FROM sales 
      ORDER BY sales_date DESC 
      LIMIT 6";
if ($res = $conn->query($q)) {
    while ($r = $res->fetch_assoc()) $recentSales[] = $r;
}

// ✅ top products by quantity sold
$q = "SELECT product_name, SUM(quantity) AS sold_qty 
      FROM sales 
      GROUP BY product_name 
      ORDER BY sold_qty DESC 
      LIMIT 6";
if ($res = $conn->query($q)) {
    while ($r = $res->fetch_assoc()) $topProducts[] = $r;
}

// ✅ out of stock items
$q = "SELECT product_name, category, quantity 
      FROM inventory 
      WHERE stock_status = 'Out of Stock' OR quantity <= 0";
if ($res = $conn->query($q)) {
    while ($p = $res->fetch_assoc()) $outOfStock[] = $p;
}

// ✅ monthly sales trend (12 months)
$monthlyLabels = [];
$monthlyData = [];
$q = "
    SELECT DATE_FORMAT(sales_date, '%Y-%m') AS ym, IFNULL(SUM(total_amount),0) AS total
    FROM sales
    WHERE sales_date >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
    GROUP BY ym
    ORDER BY ym ASC
";
if ($res = $conn->query($q)) {
    $monthMap = [];
    while ($r = $res->fetch_assoc()) $monthMap[$r['ym']] = (float)$r['total'];
    for ($i = 11; $i >= 0; $i--) {
        $m = date('Y-m', strtotime("-$i month"));
        $monthlyLabels[] = $m;
        $monthlyData[] = $monthMap[$m] ?? 0;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="styles/dashboard.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/sidebar.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="admin/inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="admin/suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="admin/costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="admin/income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="admin/sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="admin/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="admin/customers.php"><i class="fas fa-users"></i> Customers</a></li>
        <li><a href="admin/roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
        <li><a href="admin/reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
      </ul>
      <a href="admin/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </aside>

    <main class="main-content">
      <header>
        <div class="top-bar">
          <div class="logo"><img src="assets/logo.jpg" alt="Logo" style="height:50px;"></div>
          <div class="search-bar"><input type="text" placeholder="Type for search"></div>
          <div class="user-icons">
            <span class="icon"><i class="fas fa-bell"></i></span>
            <span class="icon"><i class="fas fa-comments"></i></span>
            <a href="profile.php"><span class="icon"><i class="fas fa-user-circle"></i></span></a>
          </div>
        </div>
      </header>

      <section class="dashboard-cards">
        <div class="card"><i class="fas fa-box card-icon"></i><h3>Total Products</h3><p><?= number_format($totalProducts) ?></p></div>
        <div class="card"><i class="fas fa-th card-icon"></i><h3>Total Categories</h3><p><?= number_format($totalCategories) ?></p></div>
        <div class="card"><i class="fas fa-shopping-cart card-icon"></i><h3>Total Sales</h3><p><?= number_format($totalSales) ?></p></div>
        <div class="card"><i class="fas fa-dollar-sign card-icon"></i><h3>Total Revenue</h3><p>LKR <?= number_format($totalIncome, 2) ?></p></div>
        <div class="card"><i class="fas fa-users card-icon"></i><h3>Active Customers</h3><p><?= number_format($activeCustomers) ?></p></div>
      </section>

      <section class="charts-row">
        <div class="chart-card">
          <h3>Monthly Revenue (Last 12 Months)</h3>
          <canvas id="monthlyChart"></canvas>
        </div>
        <div class="top-products card">
          <h3>Top Products</h3>
          <ul>
            <?php foreach ($topProducts as $p): ?>
              <li><?= htmlspecialchars($p['product_name']) ?> <span class="muted">(<?= intval($p['sold_qty']) ?>)</span></li>
            <?php endforeach; ?>
            <?php if (empty($topProducts)): ?><li>No products yet</li><?php endif; ?>
          </ul>
        </div>
      </section>

  <section class="content-row horizontal-section">
  <div class="card recent-sales">
    <h3>Recent Sales</h3>
    <ul>
      <?php if (!empty($recentSales)): ?>
        <?php foreach ($recentSales as $s): ?>
          <li>
            <?= htmlspecialchars($s['product_name']) ?> — <?= intval($s['quantity']) ?> — 
            LKR <?= number_format($s['total_amount'],2) ?>
            <span class="muted"><?= htmlspecialchars(date('Y-m-d', strtotime($s['sales_date']))) ?></span>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <li>No recent sales</li>
      <?php endif; ?>
    </ul>
  </div>

  <div class="card out-of-stock">
    <h3>Out of Stock</h3>
    <table class="out-of-stock-table">
      <thead><tr><th>Product</th><th>Category</th><th>Qty</th></tr></thead>
      <tbody>
        <?php if (!empty($outOfStock)): ?>
          <?php foreach ($outOfStock as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['product_name']) ?></td>
              <td><?= htmlspecialchars($p['category']) ?></td>
              <td><?= htmlspecialchars($p['quantity']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3">No out-of-stock products found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

  <script>
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    const labels = <?= json_encode($monthlyLabels) ?>;
    const data = <?= json_encode($monthlyData) ?>;
    new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Revenue',
          data,
          fill: true,
          tension: 0.3,
          borderWidth: 2,
          borderColor: '#4e73df',
          backgroundColor: 'rgba(78,115,223,0.1)'
        }]
      },
      options: {
        responsive: true,
        scales: { x: { display: true }, y: { display: true } }
      }
    });
  </script>
</body>
</html>
