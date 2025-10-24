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

// Fetch costs data
$sql = "SELECT id, costCategory, costDescription, costAmount, costDate, created_at FROM costs ORDER BY costDate DESC";
$result = $conn->query($sql);

if ($result === false) {
    die('Query failed: ' . $conn->error);
}

// Prepare data for bar chart and table
$costsData = [];
while ($row = $result->fetch_assoc()) {
    $costsData[] = [
        'id' => $row['id'],
        'costCategory' => $row['costCategory'],
        'costDescription' => $row['costDescription'],
        'costAmount' => $row['costAmount'],
        'costDate' => $row['costDate'],
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
    <title>Costs Report</title>
    <link rel="stylesheet" href="../styles/sidebar.css">
    <link rel="stylesheet" href="../styles/costsReport.css">
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
        <li><a href="costs.php" class="active"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
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
    <h1 style="text-align: center;">Costs Report</h1>

    <!-- Costs Table -->
    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cost Category</th>
                <th>Cost Description</th>
                <th>Cost Amount</th>
                <th>Cost Date</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($costsData as $data) {
            echo '<tr>';
            echo '<td>' . $data['id'] . '</td>';
            echo '<td>' . htmlspecialchars($data['costCategory']) . '</td>';
            echo '<td>' . htmlspecialchars($data['costDescription']) . '</td>';
            echo '<td>' . number_format($data['costAmount'], 2) . '</td>';
            echo '<td>' . htmlspecialchars($data['costDate']) . '</td>';
            echo '<td>' . htmlspecialchars($data['created_at']) . '</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>

    <!-- Bar Chart -->
    <canvas id="costsChart" width="800" height="400"></canvas>
    <script>
        const costsData = <?php echo json_encode($costsData); ?>;
        const labels = costsData.map(item => item.costCategory);
        const amounts = costsData.map(item => item.costAmount);

        const ctx = document.getElementById('costsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Cost Amount',
                        data: amounts,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
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
