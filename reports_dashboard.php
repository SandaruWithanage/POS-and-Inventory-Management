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

// Fetch data for reports
$budgetQuery = "SELECT COUNT(*) AS total_budgets, SUM(amount) AS total_budget_amount FROM budget";
$salesQuery = "SELECT COUNT(*) AS total_sales, SUM(total_price) AS total_sales_amount FROM sales";
$inventoryQuery = "SELECT COUNT(*) AS total_products, SUM(quantity) AS total_stock FROM inventory";

$budgetStats = $conn->query($budgetQuery)->fetch_assoc();
$salesStats = $conn->query($salesQuery)->fetch_assoc();
$inventoryStats = $conn->query($inventoryQuery)->fetch_assoc();

// Fetch detailed budget data
$sql = "SELECT id, start_date, end_date, amount, description, created_at FROM budget ORDER BY start_date DESC";
$result = $conn->query($sql);

if ($result === false) {
    die('Query failed: ' . $conn->error);
}

$budgetData = [];
while ($row = $result->fetch_assoc()) {
    $budgetData[] = [
        'id' => $row['id'],
        'start_date' => $row['start_date'],
        'end_date' => $row['end_date'],
        'amount' => $row['amount'],
        'description' => $row['description'],
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
    <title>Reports Dashboard</title>
    <link rel="stylesheet" href="../styles/sidebar.css">
    <link rel="stylesheet" href="../styles/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-cards {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }

        .card {
            background: #f4f4f4;
            border: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 30%;
        }

        .card h3 {
            margin-bottom: 10px;
        }

        table {
            margin: 20px auto;
            width: 90%;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #f4f4f4;
        }

        canvas {
            display: block;
            margin: 20px auto;
        }
    </style>
</head>
<body>
<aside class="sidebar">
      <ul>
        <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.php" class="active"><i class="fas fa-coins"></i> Budget</a></li>
        <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
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
    <h1 style="text-align: center;">Reports Dashboard</h1>

    <!-- Quick Stats -->
    <section class="dashboard-cards">
        <div class="card">
            <h3>Total Budgets</h3>
            <p><?= $budgetStats['total_budgets'] ?? 0; ?></p>
        </div>
        <div class="card">
            <h3>Total Budget Amount</h3>
            <p>LKR <?= number_format($budgetStats['total_budget_amount'] ?? 0, 2); ?></p>
        </div>
        <div class="card">
            <h3>Total Sales Amount</h3>
            <p>LKR <?= number_format($salesStats['total_sales_amount'] ?? 0, 2); ?></p>
        </div>
    </section>

    <!-- Detailed Budget Report -->
    <h2 style="text-align: center;">Budget Details</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Amount (LKR)</th>
                <th>Description</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($budgetData as $data) {
            echo '<tr>';
            echo '<td>' . $data['id'] . '</td>';
            echo '<td>' . htmlspecialchars($data['start_date']) . '</td>';
            echo '<td>' . htmlspecialchars($data['end_date']) . '</td>';
            echo '<td>' . number_format($data['amount'], 2) . '</td>';
            echo '<td>' . htmlspecialchars($data['description']) . '</td>';
            echo '<td>' . htmlspecialchars($data['created_at']) . '</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>

    <!-- Bar Chart -->
    <canvas id="reportChart" width="800" height="400"></canvas>
    <script>
        const budgetData = <?php echo json_encode($budgetData); ?>;
        const labels = budgetData.map(item => `${item.start_date} - ${item.end_date}`);
        const amounts = budgetData.map(item => item.amount);

        const ctx = document.getElementById('reportChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Budget Amount (LKR)',
                        data: amounts,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
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
