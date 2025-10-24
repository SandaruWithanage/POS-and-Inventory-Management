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

// Fetch budget data
$sql = "SELECT id, start_date, end_date, amount, description, created_at FROM budget ORDER BY start_date DESC";
$result = $conn->query($sql);

if ($result === false) {
    die('Query failed: ' . $conn->error);
}

// Prepare data for bar chart and table
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
    <title>Budget Report</title>
    <link rel="stylesheet" href="../styles/sidebar.css">
    <link rel="stylesheet" href="../styles/budgetReport.css">
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
    <h1 style="text-align: center;">Budget Report</h1>

    <!-- Budget Table -->
    <table border="1" cellspacing="0" cellpadding="10">
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
    <canvas id="budgetChart" width="800" height="400"></canvas>
    <script>
        const budgetData = <?php echo json_encode($budgetData); ?>;
        const labels = budgetData.map(item => `${item.start_date} - ${item.end_date}`);
        const amounts = budgetData.map(item => item.amount);

        const ctx = document.getElementById('budgetChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Budget Amount (LKR)',
                        data: amounts,
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