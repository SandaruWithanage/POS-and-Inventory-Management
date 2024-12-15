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

// Fetch inventory data
// Fetch inventory data
$sql = "SELECT id, product_name, barcode_no, category, quantity, unit_price, selling_price, product_image, created_at, updated_at, stock_status FROM inventory ORDER BY product_name ASC";
$result = $conn->query($sql);

// Prepare data for bar chart and table
$chartData = [];
while ($row = $result->fetch_assoc()) {
    $chartData[] = [
        'id' => $row['id'],
        'product_name' => $row['product_name'],
        'barcode_no' => $row['barcode_no'],
        'category' => $row['category'],
        'quantity' => $row['quantity'],
        'unit_price' => $row['unit_price'],
        'selling_price' => $row['selling_price'],
        'total_value' => $row['quantity'] * $row['selling_price'],
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at'],
        'stock_status' => $row['stock_status']
    ];
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Report</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
    <link rel="stylesheet" href="../styles/inventoryReport.css">
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
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.php"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
        <li><a href="purchases.php"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <button id="logout-btn" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
    </aside>

    <main class="content">
        <h1>Product Report</h1>

        <!-- Inventory Table -->
        <table border="1" cellspacing="0" cellpadding="10">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Barcode No</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Selling Price</th>
                    <th>Total Value</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Stock Status</th>
                </tr>
            </thead>
            <tbody>
    <?php
    foreach ($chartData as $data) {
        echo '<tr>';
        echo '<td>' . $data['id'] . '</td>';
        echo '<td>' . htmlspecialchars($data['product_name']) . '</td>';
        echo '<td>' . htmlspecialchars($data['barcode_no']) . '</td>';
        echo '<td>' . htmlspecialchars($data['category']) . '</td>';
        echo '<td>' . $data['quantity'] . '</td>';
        echo '<td>' . number_format($data['unit_price'], 2) . '</td>';
        echo '<td>' . number_format($data['selling_price'], 2) . '</td>';
        echo '<td>' . number_format($data['total_value'], 2) . '</td>';
        echo '<td>' . htmlspecialchars($data['created_at']) . '</td>';
        echo '<td>' . htmlspecialchars($data['updated_at']) . '</td>';
        echo '<td>' . htmlspecialchars($data['stock_status']) . '</td>';
        echo '</tr>';
    }
    ?>
</tbody>

        </table>

        <!-- Bar Chart -->
        <canvas id="inventoryChart" width="800" height="400"></canvas>
        <script>
            const chartData = <?php echo json_encode($chartData); ?>;
            const labels = chartData.map(item => item.product_name);
            const quantities = chartData.map(item => item.quantity);
            const totalValues = chartData.map(item => item.total_value);

            const ctx = document.getElementById('inventoryChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Quantity',
                            data: quantities,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Total Value',
                            data: totalValues,
                            backgroundColor: 'rgba(153, 102, 255, 0.6)',
                            borderColor: 'rgba(153, 102, 255, 1)',
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
