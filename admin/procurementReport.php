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

// Fetch order data
// Fetch order data
$sql = "SELECT id, order_name, order_date, order_status, order_value FROM `orders` ORDER BY order_date DESC";
$result = $conn->query($sql);

if ($result === false) {
    die('Query failed: ' . $conn->error);
}

// Prepare data for bar chart and table
$orderData = [];
while ($row = $result->fetch_assoc()) {
    $orderData[] = [
        'id' => $row['id'],
        'order_name' => $row['order_name'],
        'order_date' => $row['order_date'],
        'order_status' => $row['order_status'],
        'order_value' => $row['order_value']
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procument Report</title>
    <link rel="stylesheet" href="../styles/sidebar.css">
    <link rel="stylesheet" href="../styles/orderReport.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.php"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purchase.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </aside>

<main class="content">
    <h1>Procument Report</h1>

    <!-- Order Table -->
    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Order Name</th>
                <th>Order Date</th>
                <th>Order Status</th>
                <th>Order Value</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($orderData as $data) {
            echo '<tr>';
            echo '<td>' . $data['id'] . '</td>';
            echo '<td>' . htmlspecialchars($data['order_name']) . '</td>';
            echo '<td>' . htmlspecialchars($data['order_date']) . '</td>';
            echo '<td>' . htmlspecialchars($data['order_status']) . '</td>';
            echo '<td>' . number_format($data['order_value'], 2) . '</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>

    <!-- Bar Chart -->
    <canvas id="orderChart" width="800" height="400"></canvas>
    <script>
        const orderData = <?php echo json_encode($orderData); ?>;
        const labels = orderData.map(item => item.order_name);
        const values = orderData.map(item => item.order_value);

        const ctx = document.getElementById('orderChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Order Value',
                        data: values,
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
