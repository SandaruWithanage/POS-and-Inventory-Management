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

// Fetch sales data
$sql = "SELECT id, product_name, quantity, unit_price, selling_price, total_amount, sales_date FROM sales ORDER BY sales_date DESC";
$result = $conn->query($sql);

if ($result === false) {
    die('Query failed: ' . $conn->error);
}

// Prepare data for bar chart and table
$salesData = [];
while ($row = $result->fetch_assoc()) {
    $salesData[] = [
        'id' => $row['id'],
        'product_name' => $row['product_name'],
        'quantity' => $row['quantity'],
        'unit_price' => $row['unit_price'],
        'selling_price' => $row['selling_price'],
        'total_amount' => $row['total_amount'],
        'sales_date' => $row['sales_date']
    ];
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
    <link rel="stylesheet" href="../styles/salesReport.css">
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
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="sales.php" class="active"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.php"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purchase.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</a>
</aside>

<main class="content">
    <h1 style="text-align: center;">Sales Report</h1>

    <!-- Sales Table -->
    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Selling Price</th>
                <th>Total Amount</th>
                <th>Sales Date</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($salesData as $data) {
            echo '<tr>';
            echo '<td>' . $data['id'] . '</td>';
            echo '<td>' . htmlspecialchars($data['product_name']) . '</td>';
            echo '<td>' . $data['quantity'] . '</td>';
            echo '<td>' . number_format($data['unit_price'], 2) . '</td>';
            echo '<td>' . number_format($data['selling_price'], 2) . '</td>';
            echo '<td>' . number_format($data['total_amount'], 2) . '</td>';
            echo '<td>' . htmlspecialchars($data['sales_date']) . '</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>

    <!-- Bar Chart -->
    <canvas id="salesChart" width="800" height="400"></canvas>
    <script>
        const salesData = <?php echo json_encode($salesData); ?>;
        const labels = salesData.map(item => item.product_name);
        const totalAmounts = salesData.map(item => item.total_amount);

        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Sales Amount',
                        data: totalAmounts,
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
