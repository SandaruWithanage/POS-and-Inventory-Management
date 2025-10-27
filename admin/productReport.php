<?php
// ==========================
// DATABASE CONNECTION
// ==========================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ==========================
// ✅ INVENTORY SUMMARY
// ==========================
$totalQuery = "SELECT 
                  COUNT(id) AS total_products,
                  SUM(quantity) AS total_stock,
                  SUM(total_value) AS total_value
               FROM inventory";
$totalResult = $conn->query($totalQuery);
if (!$totalResult) {
    die("Query failed: " . $conn->error);
}
$totals = $totalResult->fetch_assoc();
$totalProducts = $totals['total_products'] ?? 0;
$totalStock = $totals['total_stock'] ?? 0;
$totalValue = $totals['total_value'] ?? 0;

// ==========================
// ✅ CATEGORY DISTRIBUTION
// ==========================
$categoryQuery = "
    SELECT category, SUM(quantity) AS total_qty, SUM(total_value) AS total_val
    FROM inventory
    GROUP BY category
    ORDER BY total_val DESC";
$categoryResult = $conn->query($categoryQuery);

$categories = [];
$categoryQty = [];
$categoryVal = [];
if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row['category'];
        $categoryQty[] = (int)$row['total_qty'];
        $categoryVal[] = (float)$row['total_val'];
    }
}

// ==========================
// ✅ STOCK STATUS DISTRIBUTION
// ==========================
$statusQuery = "
    SELECT stock_status, COUNT(*) AS total
    FROM inventory
    GROUP BY stock_status";
$statusResult = $conn->query($statusQuery);

$stockStatuses = [];
$statusCounts = [];
if ($statusResult) {
    while ($row = $statusResult->fetch_assoc()) {
        $stockStatuses[] = ucfirst($row['stock_status']);
        $statusCounts[] = (int)$row['total'];
    }
}

// ==========================
// ✅ SUPPLIER WISE VALUE
// ==========================
$supplierQuery = "
    SELECT s.supplierName, SUM(i.total_value) AS total_value
    FROM inventory i
    LEFT JOIN suppliers s ON i.supplier_id = s.id
    GROUP BY s.supplierName
    ORDER BY total_value DESC";
$supplierResult = $conn->query($supplierQuery);

$supplierNames = [];
$supplierValues = [];
if ($supplierResult) {
    while ($row = $supplierResult->fetch_assoc()) {
        $supplierNames[] = $row['supplierName'] ?? "Unknown";
        $supplierValues[] = (float)$row['total_value'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product (Inventory) Report</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/dashboard.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/html2canvas"></script>
  <script src="https://cdn.jsdelivr.net/npm/jspdf"></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f7fa;
      color: #333;
    }
    .main-content {
      padding: 30px;
    }
    h1, h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 20px;
    }
    .summary {
      display: flex;
      justify-content: space-around;
      margin: 30px 0;
      flex-wrap: wrap;
    }
    .summary-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 20px 30px;
      margin: 10px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .summary-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    }
    .summary-card h3 {
      font-size: 16px;
      color: #555;
      margin-bottom: 10px;
    }
    .summary-card p {
      font-size: 20px;
      font-weight: 600;
    }
    canvas {
      background: #fff;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 40px;
    }
    .pdf-btn {
      display: block;
      margin: 20px auto;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 10px 25px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .pdf-btn:hover {
      background-color: #0056b3;
      transform: scale(1.05);
    }
  </style>
</head>

<body>
  <aside class="sidebar">
    <ul>
      <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="inventory.php" class="active"><i class="fas fa-boxes"></i> Inventory</a></li>
      <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
      <li><a href="budget.php"><i class="fas fa-coins"></i> Budget</a></li>
      <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
      <li><a href="income.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
      <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
      <li><a href="financialReport.php"><i class="fas fa-chart-pie"></i> Financial Report</a></li>
    </ul>
    <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
  </aside>

  <main class="main-content" id="reportContent">
    <h1>📦 Product (Inventory) Report</h1>

    <!-- Summary Cards -->
    <div class="summary">
      <div class="summary-card" style="border-left:5px solid #007bff;">
        <h3>Total Products</h3>
        <p><?= number_format($totalProducts) ?></p>
      </div>
      <div class="summary-card" style="border-left:5px solid #28a745;">
        <h3>Total Stock Quantity</h3>
        <p><?= number_format($totalStock) ?></p>
      </div>
      <div class="summary-card" style="border-left:5px solid #ffc107;">
        <h3>Total Stock Value</h3>
        <p>LKR <?= number_format($totalValue, 2) ?></p>
      </div>
    </div>

    <h2>📊 Category-Wise Inventory Value</h2>
    <canvas id="categoryValueChart"></canvas>

    <h2>📈 Category-Wise Quantity</h2>
    <canvas id="categoryQtyChart"></canvas>

    <h2>🏭 Supplier Contribution</h2>
    <canvas id="supplierChart"></canvas>

    <h2>📦 Stock Status Overview</h2>
    <canvas id="stockStatusChart"></canvas>

    <button class="pdf-btn" onclick="downloadPDF()">📥 Download PDF Report</button>
  </main>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
  <script>
    // Category Value Chart
    new Chart(document.getElementById('categoryValueChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode($categories) ?>,
        datasets: [{
          label: 'Category Stock Value (LKR)',
          data: <?= json_encode($categoryVal) ?>,
          backgroundColor: '#007bff'
        }]
      },
      options: {
        responsive: true,
        plugins: { title: { display: true, text: 'Inventory Value by Category' } },
        scales: { y: { beginAtZero: true } }
      }
    });

    // Category Quantity Chart
    new Chart(document.getElementById('categoryQtyChart'), {
      type: 'line',
      data: {
        labels: <?= json_encode($categories) ?>,
        datasets: [{
          label: 'Total Quantity',
          data: <?= json_encode($categoryQty) ?>,
          borderColor: '#28a745',
          fill: true,
          backgroundColor: 'rgba(40,167,69,0.1)'
        }]
      },
      options: {
        responsive: true,
        plugins: { title: { display: true, text: 'Stock Quantity by Category' } },
        scales: { y: { beginAtZero: true } }
      }
    });

    // Supplier Contribution Chart
    new Chart(document.getElementById('supplierChart'), {
      type: 'pie',
      data: {
        labels: <?= json_encode($supplierNames) ?>,
        datasets: [{
          data: <?= json_encode($supplierValues) ?>,
          backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#dc3545', '#6f42c1']
        }]
      },
      options: {
        plugins: { title: { display: true, text: 'Supplier-Wise Inventory Value' } }
      }
    });

    // Stock Status Chart
    new Chart(document.getElementById('stockStatusChart'), {
      type: 'doughnut',
      data: {
        labels: <?= json_encode($stockStatuses) ?>,
        datasets: [{
          data: <?= json_encode($statusCounts) ?>,
          backgroundColor: ['#28a745', '#ffc107', '#dc3545']
        }]
      },
      options: {
        plugins: { title: { display: true, text: 'Stock Status Overview' } }
      }
    });

    // PDF Export
    function downloadPDF() {
      html2canvas(document.querySelector("#reportContent")).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');
        const imgWidth = 210;
        const pageHeight = 297;
        const imgHeight = canvas.height * imgWidth / canvas.width;
        let heightLeft = imgHeight;
        let position = 0;
        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
        while (heightLeft >= 0) {
          position = heightLeft - imgHeight;
          pdf.addPage();
          pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
          heightLeft -= pageHeight;
        }
        pdf.save('Product_Inventory_Report.pdf');
      });
    }
  </script>
</body>
</html>
