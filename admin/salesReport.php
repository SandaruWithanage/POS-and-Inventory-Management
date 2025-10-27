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
// ✅ TOTAL SALES SUMMARY
// ==========================
$totalQuery = "SELECT 
                  SUM(total_amount) AS total_sales,
                  COUNT(id) AS total_orders,
                  SUM(quantity) AS total_items
               FROM sales";
$totalResult = $conn->query($totalQuery);

if (!$totalResult) {
    die("Query failed: " . $conn->error);
}

$totals = $totalResult->fetch_assoc();
$totalSales = $totals['total_sales'] ?? 0;
$totalOrders = $totals['total_orders'] ?? 0;
$totalItems = $totals['total_items'] ?? 0;

// ==========================
// ✅ MONTHLY SALES
// ==========================
$monthlyQuery = "
    SELECT 
        DATE_FORMAT(sales_date, '%Y-%m') AS month,
        SUM(total_amount) AS total
    FROM sales
    GROUP BY month
    ORDER BY month ASC";
$monthlyResult = $conn->query($monthlyQuery);

$months = [];
$monthlySales = [];
if ($monthlyResult) {
    while ($row = $monthlyResult->fetch_assoc()) {
        $months[] = $row['month'];
        $monthlySales[] = (float)$row['total'];
    }
}

// ==========================
// ✅ ANNUAL SALES
// ==========================
$annualQuery = "
    SELECT 
        YEAR(sales_date) AS year,
        SUM(total_amount) AS total
    FROM sales
    GROUP BY year
    ORDER BY year ASC";
$annualResult = $conn->query($annualQuery);

$years = [];
$annualSales = [];
if ($annualResult) {
    while ($row = $annualResult->fetch_assoc()) {
        $years[] = $row['year'];
        $annualSales[] = (float)$row['total'];
    }
}

// ==========================
// ✅ TOP SELLING PRODUCTS
// ==========================
$topProductsQuery = "
    SELECT product_name, SUM(quantity) AS total_sold
    FROM sales
    GROUP BY product_name
    ORDER BY total_sold DESC
    LIMIT 5";
$topProductsResult = $conn->query($topProductsQuery);

$topNames = [];
$topQuantities = [];
if ($topProductsResult) {
    while ($row = $topProductsResult->fetch_assoc()) {
        $topNames[] = $row['product_name'];
        $topQuantities[] = (int)$row['total_sold'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sales Report</title>
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
      <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
      <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
      <li><a href="budget.php"><i class="fas fa-coins"></i> Budget</a></li>
      <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
      <li><a href="income.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
      <li><a href="sales.php" class="active"><i class="fas fa-chart-line"></i> Sales</a></li>
      <li><a href="financialReport.php"><i class="fas fa-chart-pie"></i> Financial Report</a></li>
    </ul>
    <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
  </aside>

  <main class="main-content" id="reportContent">
    <h1>📊 Sales Report</h1>

    <!-- Summary Cards -->
    <div class="summary">
      <div class="summary-card" style="border-left:5px solid #007bff;">
        <h3>Total Sales</h3>
        <p>LKR <?= number_format($totalSales, 2) ?></p>
      </div>
      <div class="summary-card" style="border-left:5px solid #28a745;">
        <h3>Total Orders</h3>
        <p><?= number_format($totalOrders) ?></p>
      </div>
      <div class="summary-card" style="border-left:5px solid #ffc107;">
        <h3>Total Items Sold</h3>
        <p><?= number_format($totalItems) ?></p>
      </div>
    </div>

    <h2>📅 Monthly Sales</h2>
    <canvas id="monthlyChart"></canvas>

    <h2>📆 Annual Sales</h2>
    <canvas id="annualChart"></canvas>

    <h2>🏆 Top 5 Selling Products</h2>
    <canvas id="topProductsChart"></canvas>

    <button class="pdf-btn" onclick="downloadPDF()">📥 Download PDF Report</button>
  </main>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
  <script>
    // Monthly Sales Chart
    new Chart(document.getElementById('monthlyChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
          label: 'Monthly Sales (LKR)',
          data: <?= json_encode($monthlySales) ?>,
          backgroundColor: '#007bff'
        }]
      },
      options: {
        responsive: true,
        plugins: { title: { display: true, text: 'Monthly Sales Overview' } },
        scales: { y: { beginAtZero: true } }
      }
    });

    // Annual Sales Chart
    new Chart(document.getElementById('annualChart'), {
      type: 'line',
      data: {
        labels: <?= json_encode($years) ?>,
        datasets: [{
          label: 'Annual Sales (LKR)',
          data: <?= json_encode($annualSales) ?>,
          borderColor: '#28a745',
          fill: true,
          backgroundColor: 'rgba(40,167,69,0.1)'
        }]
      },
      options: {
        responsive: true,
        plugins: { title: { display: true, text: 'Annual Sales Trend' } },
        scales: { y: { beginAtZero: true } }
      }
    });

    // Top Products Chart
    new Chart(document.getElementById('topProductsChart'), {
      type: 'pie',
      data: {
        labels: <?= json_encode($topNames) ?>,
        datasets: [{
          data: <?= json_encode($topQuantities) ?>,
          backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#dc3545']
        }]
      },
      options: {
        plugins: { title: { display: true, text: 'Top 5 Best Selling Products' } }
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
        pdf.save('Sales_Report.pdf');
      });
    }
  </script>
</body>
</html>
