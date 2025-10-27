<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// =========================
// ✅ Overall Totals
// =========================
$sql = "SELECT 
            SUM(income_amount) AS total_income,
            SUM(cost_amount) AS total_cost,
            SUM(sales_amount) AS total_sales
        FROM income";
$result = $conn->query($sql);
$totals = $result->fetch_assoc();

$totalIncome = $totals['total_income'] ?? 0;
$totalCost   = $totals['total_cost'] ?? 0;
$totalSales  = $totals['total_sales'] ?? 0;
$totalProfit = $totalSales + $totalIncome - $totalCost;

// =========================
// ✅ Monthly Totals
// =========================
$monthlyQuery = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        SUM(income_amount) AS income,
        SUM(cost_amount) AS cost,
        SUM(sales_amount) AS sales
    FROM income
    GROUP BY month
    ORDER BY month ASC
";
$monthlyResult = $conn->query($monthlyQuery);

$months = [];
$monthlyIncome = [];
$monthlyCost = [];
$monthlySales = [];

while ($row = $monthlyResult->fetch_assoc()) {
    $months[] = $row['month'];
    $monthlyIncome[] = (float)$row['income'];
    $monthlyCost[] = (float)$row['cost'];
    $monthlySales[] = (float)$row['sales'];
}

// =========================
// ✅ Annual Totals
// =========================
$annualQuery = "
    SELECT 
        YEAR(created_at) AS year,
        SUM(income_amount) AS income,
        SUM(cost_amount) AS cost,
        SUM(sales_amount) AS sales
    FROM income
    GROUP BY year
    ORDER BY year ASC
";
$annualResult = $conn->query($annualQuery);

$years = [];
$annualIncome = [];
$annualCost = [];
$annualSales = [];

while ($row = $annualResult->fetch_assoc()) {
    $years[] = $row['year'];
    $annualIncome[] = (float)$row['income'];
    $annualCost[] = (float)$row['cost'];
    $annualSales[] = (float)$row['sales'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Financial Report</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/dashboard.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/html2canvas"></script>
  <script src="https://cdn.jsdelivr.net/npm/jspdf"></script>
  <style>
    body {
      font-family: "Poppins", sans-serif;
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
      <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
      <li><a href="financialReport.php" class="active"><i class="fas fa-chart-pie"></i> Financial Report</a></li>
    </ul>
    <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
  </aside>

  <main class="main-content" id="reportContent">
    <h1>📊 Financial Overview</h1>

    <!-- Summary Cards -->
    <div class="summary">
      <div class="summary-card" style="border-left:5px solid #28a745;">
        <h3>Total Income</h3>
        <p>LKR <?= number_format($totalIncome, 2) ?></p>
      </div>
      <div class="summary-card" style="border-left:5px solid #007bff;">
        <h3>Total Sales</h3>
        <p>LKR <?= number_format($totalSales, 2) ?></p>
      </div>
      <div class="summary-card" style="border-left:5px solid #dc3545;">
        <h3>Total Costs</h3>
        <p>LKR <?= number_format($totalCost, 2) ?></p>
      </div>
      <div class="summary-card" style="border-left:5px solid #ffc107;">
        <h3>Net Profit</h3>
        <p>LKR <?= number_format($totalProfit, 2) ?></p>
      </div>
    </div>

    <h2>📅 Monthly Report</h2>
    <canvas id="monthlyChart"></canvas>

    <h2>📆 Annual Report</h2>
    <canvas id="annualChart"></canvas>

    <h2>💰 Financial Distribution</h2>
    <canvas id="financePieChart"></canvas>

    <button class="pdf-btn" onclick="downloadPDF()">📥 Download PDF Report</button>
  </main>

  <script>
    // ========================
    // Monthly Chart
    // ========================
    new Chart(document.getElementById('monthlyChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode($months) ?>,
        datasets: [
          { label: 'Income', data: <?= json_encode($monthlyIncome) ?>, backgroundColor: '#28a745' },
          { label: 'Sales', data: <?= json_encode($monthlySales) ?>, backgroundColor: '#007bff' },
          { label: 'Costs', data: <?= json_encode($monthlyCost) ?>, backgroundColor: '#dc3545' }
        ]
      },
      options: {
        responsive: true,
        plugins: { title: { display: true, text: 'Monthly Financial Overview' } },
        scales: { y: { beginAtZero: true } }
      }
    });

    // ========================
    // Annual Chart
    // ========================
    new Chart(document.getElementById('annualChart'), {
      type: 'line',
      data: {
        labels: <?= json_encode($years) ?>,
        datasets: [
          { label: 'Income', data: <?= json_encode($annualIncome) ?>, borderColor: '#28a745', fill: false },
          { label: 'Sales', data: <?= json_encode($annualSales) ?>, borderColor: '#007bff', fill: false },
          { label: 'Costs', data: <?= json_encode($annualCost) ?>, borderColor: '#dc3545', fill: false }
        ]
      },
      options: {
        responsive: true,
        plugins: { title: { display: true, text: 'Annual Financial Performance' } },
        scales: { y: { beginAtZero: true } }
      }
    });

    // ========================
    // Pie Chart
    // ========================
    new Chart(document.getElementById('financePieChart'), {
      type: 'pie',
      data: {
        labels: ['Income', 'Sales', 'Costs'],
        datasets: [{
          data: [<?= $totalIncome ?>, <?= $totalSales ?>, <?= $totalCost ?>],
          backgroundColor: ['#28a745', '#007bff', '#dc3545']
        }]
      },
      options: {
        plugins: { title: { display: true, text: 'Overall Financial Distribution' } }
      }
    });

    // ========================
    // PDF Export
    // ========================
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
        pdf.save('Financial_Report.pdf');
      });
    }
  </script>
</body>
</html>
