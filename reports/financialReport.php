<?php
// =====================================
// Database Connection
// =====================================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// =====================================
// Fetch Summary Data
// =====================================

// Total Income
$incomeResult = $conn->query("SELECT IFNULL(SUM(incomeAmount), 0) AS total_income FROM income_costs");
$totalIncome = ($incomeResult && $incomeResult->num_rows > 0) ? $incomeResult->fetch_assoc()['total_income'] : 0;

// Total Costs
$costResult = $conn->query("SELECT IFNULL(SUM(costAmount), 0) AS total_cost FROM costs");
$totalCost = ($costResult && $costResult->num_rows > 0) ? $costResult->fetch_assoc()['total_cost'] : 0;

// Total Sales
$salesResult = $conn->query("SELECT IFNULL(SUM(total_amount), 0) AS total_sales FROM sales");
$totalSales = ($salesResult && $salesResult->num_rows > 0) ? $salesResult->fetch_assoc()['total_sales'] : 0;

// Profit Calculation
$totalProfit = $totalIncome + $totalSales - $totalCost;

// =====================================
// Fetch Detailed Costs, Income, Sales Data
// =====================================
$costsData = $conn->query("SELECT costCategory, costAmount, costDate FROM costs ORDER BY costDate DESC")->fetch_all(MYSQLI_ASSOC);
$incomeData = $conn->query("SELECT incomeCategory, incomeAmount, incomeDate FROM income_costs ORDER BY incomeDate DESC")->fetch_all(MYSQLI_ASSOC);
$salesData = $conn->query("SELECT product_name, quantity, total_amount, sales_date FROM sales ORDER BY sales_date DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);

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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f7fa;
      margin: 0;
      display: flex;
    }
    main {
      flex: 1;
      padding: 30px;
      background: #fff;
    }
    h1 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 25px;
    }

    /* Summary Cards */
    .summary-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      text-align: center;
      padding: 20px;
      transition: transform 0.3s;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .card i {
      font-size: 28px;
      margin-bottom: 10px;
      color: #00bcd4;
    }
    .card h3 {
      margin: 5px 0;
      color: #555;
    }
    .card p {
      font-size: 20px;
      font-weight: bold;
      color: #222;
    }

    /* Charts Section */
    .charts {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 25px;
      margin-bottom: 30px;
    }
    canvas {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 15px;
    }

    /* Table */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }
    table th, table td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }
    table th {
      background-color: #007bff;
      color: white;
    }

    /* Animation */
    body {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.5s ease, transform 0.5s ease;
    }
    body.loaded {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>

<body>
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
    </ul>
    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</a>
  </aside>

  <main>
    <h1>📊 Financial Overview Report</h1>

    <!-- Summary Cards -->
    <section class="summary-cards">
      <div class="card">
        <i class="fas fa-wallet"></i>
        <h3>Total Income</h3>
        <p>LKR <?= number_format($totalIncome, 2) ?></p>
      </div>
      <div class="card">
        <i class="fas fa-money-bill-wave"></i>
        <h3>Total Costs</h3>
        <p>LKR <?= number_format($totalCost, 2) ?></p>
      </div>
      <div class="card">
        <i class="fas fa-chart-line"></i>
        <h3>Total Sales</h3>
        <p>LKR <?= number_format($totalSales, 2) ?></p>
      </div>
      <div class="card">
        <i class="fas fa-hand-holding-usd"></i>
        <h3>Net Profit</h3>
        <p>LKR <?= number_format($totalProfit, 2) ?></p>
      </div>
    </section>

    <!-- Charts -->
    <section class="charts">
      <canvas id="financePie"></canvas>
      <canvas id="financeBar"></canvas>
    </section>

    <!-- Detailed Table -->
    <h2>Recent Financial Activities</h2>
    <table>
      <thead>
        <tr>
          <th>Category</th>
          <th>Type</th>
          <th>Amount (LKR)</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($incomeData as $i): ?>
          <tr>
            <td><?= htmlspecialchars($i['incomeCategory']) ?></td>
            <td>Income</td>
            <td><?= number_format($i['incomeAmount'], 2) ?></td>
            <td><?= htmlspecialchars($i['incomeDate']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php foreach ($costsData as $c): ?>
          <tr>
            <td><?= htmlspecialchars($c['costCategory']) ?></td>
            <td>Cost</td>
            <td><?= number_format($c['costAmount'], 2) ?></td>
            <td><?= htmlspecialchars($c['costDate']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>

  <script>
    // Smooth page transition
    window.addEventListener("load", () => document.body.classList.add("loaded"));

    // Financial Summary Data for Chart
    const pieData = {
      labels: ["Income", "Costs", "Sales"],
      datasets: [{
        data: [<?= $totalIncome ?>, <?= $totalCost ?>, <?= $totalSales ?>],
        backgroundColor: ["#4caf50", "#f44336", "#2196f3"],
        hoverOffset: 10
      }]
    };

    new Chart(document.getElementById("financePie"), {
      type: "pie",
      data: pieData,
      options: {
        responsive: true,
        plugins: { legend: { position: "bottom" } }
      }
    });

    const barLabels = ["Income", "Costs", "Sales", "Profit"];
    const barValues = [<?= $totalIncome ?>, <?= $totalCost ?>, <?= $totalSales ?>, <?= $totalProfit ?>];

    new Chart(document.getElementById("financeBar"), {
      type: "bar",
      data: {
        labels: barLabels,
        datasets: [{
          label: "Amount (LKR)",
          data: barValues,
          backgroundColor: ["#4caf50", "#f44336", "#2196f3", "#ff9800"]
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } },
        responsive: true
      }
    });
  </script>
</body>
</html>
