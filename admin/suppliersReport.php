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

// Fetch supplier data
$sql = "SELECT id, supplierName, supplierEmail, supplierPhone, productSupplied FROM suppliers ORDER BY supplierName ASC";
$result = $conn->query($sql);

if ($result === false) {
    die('Query failed: ' . $conn->error);
}

// Prepare data for table
$supplierData = [];
while ($row = $result->fetch_assoc()) {
    $supplierData[] = [
        'id' => $row['id'],
        'supplierName' => $row['supplierName'],
        'supplierEmail' => $row['supplierEmail'],
        'supplierPhone' => $row['supplierPhone'],
        'productSupplied' => $row['productSupplied']
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Report</title>
    <link rel="stylesheet" href="../styles/sidebar.css">
    <link rel="stylesheet" href="../styles/supplierReport.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Centering content */
        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            text-align: center;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        canvas {
            margin: 20px auto;
            max-width: 80%;
        }

        /* Sidebar styling */
        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li a {
            display: block;
            padding: 10px;
            text-decoration: none;
        }

        .logout-btn {
            margin: 20px;
        }
    </style>
</head>
<body>
<aside class="sidebar">
      <ul>
      <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.php" class="active"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.php"><i class="fas fa-coins"></i> Budget</a></li>
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
    <h1>Supplier Report</h1>

    <!-- Supplier Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier Name</th>
                <th>Supplier Email</th>
                <th>Supplier Phone</th>
                <th>Product Supplied</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($supplierData as $data) {
            echo '<tr>';
            echo '<td>' . $data['id'] . '</td>';
            echo '<td>' . htmlspecialchars($data['supplierName']) . '</td>';
            echo '<td>' . htmlspecialchars($data['supplierEmail']) . '</td>';
            echo '<td>' . htmlspecialchars($data['supplierPhone']) . '</td>';
            echo '<td>' . htmlspecialchars($data['productSupplied']) . '</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>

    <!-- Bar Chart (for visualizing number of suppliers per product supplied) -->
    <canvas id="supplierChart" width="800" height="400"></canvas>
    <script>
        const supplierData = <?php echo json_encode($supplierData); ?>;
        const labels = [...new Set(supplierData.map(item => item.productSupplied))]; // Unique product names
        const counts = labels.map(label => supplierData.filter(item => item.productSupplied === label).length);

        const ctx = document.getElementById('supplierChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Number of Suppliers per Product',
                        data: counts,
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