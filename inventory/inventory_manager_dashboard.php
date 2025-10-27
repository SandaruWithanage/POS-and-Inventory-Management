<?php
session_start();

// --- Auth: only inventory manager allowed
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'inventory_manager') {
    header("Location: ../index.php");
    exit;
}

require_once __DIR__ . '/config/db_connect.php';

// --- KPI queries ---
$totalProducts = 0;
$totalSuppliers = 0;
$outOfStockCount = 0;
$lowStockCount = 0;
$totalInventoryValue = 0;
$outOfStock = [];
$lowStock = [];
$recentItems = [];
$categoryLabels = [];
$categoryCounts = [];

// ✅ total products
$q = "SELECT COUNT(id) AS cnt FROM inventory";
if ($r = $conn->query($q)) $totalProducts = (int)$r->fetch_assoc()['cnt'];

// ✅ total suppliers
$q = "SELECT COUNT(id) AS cnt FROM suppliers";
if ($r = $conn->query($q)) $totalSuppliers = (int)$r->fetch_assoc()['cnt'];

// ✅ out-of-stock count
$q = "SELECT COUNT(id) AS cnt FROM inventory WHERE stock_status='Out of Stock' OR quantity<=0";
if ($r = $conn->query($q)) $outOfStockCount = (int)$r->fetch_assoc()['cnt'];

// ✅ low-stock count (≤5)
$q = "SELECT COUNT(id) AS cnt FROM inventory WHERE quantity>0 AND quantity<=5";
if ($r = $conn->query($q)) $lowStockCount = (int)$r->fetch_assoc()['cnt'];

// ✅ total inventory value
$q = "SELECT IFNULL(SUM(total_value),0) AS total_val FROM inventory";
if ($r = $conn->query($q)) $totalInventoryValue = (float)$r->fetch_assoc()['total_val'];

// ✅ out of stock items
$q = "SELECT product_name, category, quantity FROM inventory WHERE stock_status='Out of Stock' OR quantity<=0 LIMIT 6";
if ($r = $conn->query($q)) while($row=$r->fetch_assoc()) $outOfStock[]=$row;

// ✅ low stock items
$q = "SELECT product_name, category, quantity FROM inventory WHERE quantity>0 AND quantity<=5 LIMIT 6";
if ($r = $conn->query($q)) while($row=$r->fetch_assoc()) $lowStock[]=$row;

// ✅ recent inventory additions
$q = "SELECT product_name, category, quantity, created_at FROM inventory ORDER BY created_at DESC LIMIT 6";
if ($r = $conn->query($q)) while($row=$r->fetch_assoc()) $recentItems[]=$row;

// ✅ category distribution
$q = "SELECT category, COUNT(*) AS cnt FROM inventory GROUP BY category ORDER BY cnt DESC";
if ($r = $conn->query($q)) {
    while($row=$r->fetch_assoc()) {
        $categoryLabels[] = $row['category'];
        $categoryCounts[] = (int)$row['cnt'];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Inventory Manager Dashboard</title>
  <link rel="stylesheet" href="../styles/dashboard.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/sidebar.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="container">
  <!-- Sidebar -->
  <aside class="sidebar">
    <ul>
      <li><a href="inventory_manager_dashboard.php" class="active"><i class="fas fa-warehouse"></i> Inventory Dashboard</a></li>
      <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
      <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
      <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
      <li><a href="reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
    </ul>
    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</a>
  </aside>

  <!-- Main -->
  <main class="main-content">
    <header>
      <div class="top-bar">
        <div class="logo"><img src="../assets/logo.jpg" alt="Logo" style="height:50px;"></div>
        <div class="search-bar"><input type="text" placeholder="Search inventory..."></div>
        <div class="user-icons">
          <span class="icon"><i class="fas fa-bell"></i></span>
          <a href="profile.php"><span class="icon"><i class="fas fa-user-circle"></i></span></a>
        </div>
      </div>
    </header>

    <section class="dashboard-cards">
      <div class="card"><i class="fas fa-box card-icon"></i><h3>Total Products</h3><p><?= number_format($totalProducts) ?></p></div>
      <div class="card"><i class="fas fa-truck card-icon"></i><h3>Total Suppliers</h3><p><?= number_format($totalSuppliers) ?></p></div>
      <div class="card"><i class="fas fa-exclamation-triangle card-icon"></i><h3>Out of Stock</h3><p><?= number_format($outOfStockCount) ?></p></div>
      <div class="card"><i class="fas fa-battery-quarter card-icon"></i><h3>Low Stock (≤5)</h3><p><?= number_format($lowStockCount) ?></p></div>
      <div class="card"><i class="fas fa-coins card-icon"></i><h3>Total Inventory Value</h3><p>LKR <?= number_format($totalInventoryValue,2) ?></p></div>
    </section>

    <section class="charts-row">
      <div class="chart-card">
        <h3>Category-wise Inventory</h3>
        <canvas id="categoryChart"></canvas>
      </div>
    </section>

    <section class="content-row horizontal-section">
      <div class="card out-of-stock">
        <h3>Out of Stock</h3>
        <table class="out-of-stock-table">
          <thead><tr><th>Product</th><th>Category</th><th>Qty</th></tr></thead>
          <tbody>
            <?php if (!empty($outOfStock)): ?>
              <?php foreach ($outOfStock as $p): ?>
                <tr><td><?= htmlspecialchars($p['product_name']) ?></td><td><?= htmlspecialchars($p['category']) ?></td><td><?= htmlspecialchars($p['quantity']) ?></td></tr>
              <?php endforeach; ?>
            <?php else: ?><tr><td colspan="3">All items in stock</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="card low-stock">
        <h3>Low Stock (≤5)</h3>
        <table class="out-of-stock-table">
          <thead><tr><th>Product</th><th>Category</th><th>Qty</th></tr></thead>
          <tbody>
            <?php if (!empty($lowStock)): ?>
              <?php foreach ($lowStock as $p): ?>
                <tr><td><?= htmlspecialchars($p['product_name']) ?></td><td><?= htmlspecialchars($p['category']) ?></td><td><?= htmlspecialchars($p['quantity']) ?></td></tr>
              <?php endforeach; ?>
            <?php else: ?><tr><td colspan="3">No low-stock items</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="card recent-added">
        <h3>Recently Added Items</h3>
        <ul>
          <?php foreach ($recentItems as $i): ?>
            <li><?= htmlspecialchars($i['product_name']) ?> — <?= htmlspecialchars($i['category']) ?> — <?= intval($i['quantity']) ?> <span class="muted"><?= date('Y-m-d', strtotime($i['created_at'])) ?></span></li>
          <?php endforeach; ?>
          <?php if (empty($recentItems)): ?><li>No recent additions</li><?php endif; ?>
        </ul>
      </div>
    </section>
  </main>
</div>

<script>
const ctx = document.getElementById('categoryChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($categoryLabels) ?>,
    datasets: [{
      label: 'Items per Category',
      data: <?= json_encode($categoryCounts) ?>,
      backgroundColor: 'rgba(78,115,223,0.5)',
      borderColor: '#4e73df',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: { y: { beginAtZero: true } }
  }
});
</script>
</body>
</html>
