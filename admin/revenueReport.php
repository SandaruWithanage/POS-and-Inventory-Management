<?php 
// Database connection
$host = 'localhost';
$username = 'root';
$password = ''; // Adjust password if necessary
$dbname = 'final_project';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Fetch revenue data
$sql = "SELECT id, sales_amount, cost_amount, income_amount, created_at FROM `income` ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result === false) {
    die('Query failed: ' . $conn->error);
}

// Prepare data for bar chart and table
$revenueData = [];
while ($row = $result->fetch_assoc()) {
    $revenueData[] = [
        'id' => $row['id'],
        'sales_amount' => $row['sales_amount'],
        'cost_amount' => $row['cost_amount'],
        'income_amount' => $row['income_amount'],
        'created_at' => $row['created_at']
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Report</title>
    <link rel="stylesheet" href="../styles/sidebar.css">
    <link rel="stylesheet" href="../styles/revenueReport.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        table {
            margin: 0 auto;
        }
        canvas {
            display: block;
            margin: 0 auto;
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
        <li><a href="income-costs.php" class="active"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.php"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purchase.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</a>
</aside>

<main class="content">
    <h1 style="text-align: center;">Revenue Report</h1>

    <!-- Revenue Table -->
    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sales Amount</th>
                <th>Cost Amount</th>
                <th>Income Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($revenueData as $data) {
            echo '<tr>';
            echo '<td>' . $data['id'] . '</td>';
            echo '<td>' . number_format($data['sales_amount'], 2) . '</td>';
            echo '<td>' . number_format($data['cost_amount'], 2) . '</td>';
            echo '<td>' . number_format($data['income_amount'], 2) . '</td>';
            echo '<td>' . htmlspecialchars($data['created_at']) . '</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>

    <!-- Bar Chart -->
    <canvas id="revenueChart" width="800" height="400"></canvas>
    <script>
        const revenueData = <?php echo json_encode($revenueData); ?>;
        const labels = revenueData.map(item => item.created_at);
        const sales = revenueData.map(item => item.sales_amount);
        const costs = revenueData.map(item => item.cost_amount);
        const incomes = revenueData.map(item => item.income_amount);

        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Sales Amount',
                        data: sales,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Cost Amount',
                        data: costs,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Income Amount',
                        data: incomes,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</main>
</body>
</html>
