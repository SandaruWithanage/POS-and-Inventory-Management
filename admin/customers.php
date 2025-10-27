<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ====================================================
// ✅ HANDLE DELETE REQUEST
// ====================================================
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $conn->query("DELETE FROM customers WHERE id = $deleteId");
    header("Location: customers.php");
    exit;
}

// ====================================================
// ✅ HANDLE ADD NEW CUSTOMER (AJAX)
// ====================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['customerName'])) {
    $customerName = trim($_POST['customerName']);
    $customerEmail = trim($_POST['customerEmail']);
    $customerPhone = trim($_POST['customerPhone']);

    if (empty($customerName) || empty($customerEmail) || empty($customerPhone)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO customers (customerName, customerEmail, customerPhone) VALUES (?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Database error."]);
        exit;
    }

    $stmt->bind_param("sss", $customerName, $customerEmail, $customerPhone);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Customer added successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add customer."]);
    }

    $stmt->close();
    exit;
}

// ====================================================
// ✅ FETCH CUSTOMERS FOR TABLE
// ====================================================
$customers = [];
$result = $conn->query("SELECT id, customerName, customerEmail, customerPhone, created_at FROM customers ORDER BY id DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Management - Royalty Program</title>
  <link rel="stylesheet" href="../styles/sidebar.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/customer.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
</head>

<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.php"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php" class="active"><i class="fas fa-users"></i> Customers</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header>
        <div class="top-bar">
          <div class="logo"><img src="../assets/logo.jpg" alt="Logo" style="height: 50px;"></div>
          <div class="search-bar"><input type="text" id="searchInput" placeholder="Type for search"></div>
          <div class="user-icons">
            <span class="icon"><i class="fas fa-bell"></i></span>
            <span class="icon"><i class="fas fa-comments"></i></span>
            <a href="profile.html"><span class="icon"><i class="fas fa-user-circle"></i></span></a>
          </div>
        </div>
      </header>

      <h1>Customer Management (Royalty Program)</h1>

      <!-- Add Customer Form -->
      <form class="add-customer-form" id="addCustomerForm">
        <div class="form-group">
          <label for="customerName">Customer Name</label>
          <input type="text" id="customerName" name="customerName" required>
        </div>
        <div class="form-group">
          <label for="customerEmail">Email Address</label>
          <input type="email" id="customerEmail" name="customerEmail" required>
        </div>
        <div class="form-group">
          <label for="customerPhone">Phone Number</label>
          <input type="text" id="customerPhone" name="customerPhone" required>
        </div>
        <div class="form-group">
          <button type="submit">Add Customer</button>
        </div>
        <p class="error-message" id="formErrorMessage"></p>
      </form>

      <!-- Royalty Card -->
      <div class="card" id="royaltyCard" style="display:none;">
        <h2>Royalty Card</h2>
        <p>Name: <span id="customerNameDisplay"></span></p>
        <p>Customer ID: <span id="customerIdDisplay"></span></p>
        <div class="qr-code" id="qrCode"></div>
        <button class="card-button" onclick="window.print()">Print Card</button>
      </div>

      <!-- Customer Table -->
      <section class="customer-table-section">
        <h2>Customer List</h2>
        <table id="customerTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Joined On</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($customers)): ?>
              <?php foreach ($customers as $c): ?>
                <tr>
                  <td><?= htmlspecialchars($c['id']) ?></td>
                  <td><?= htmlspecialchars($c['customerName']) ?></td>
                  <td><?= htmlspecialchars($c['customerEmail']) ?></td>
                  <td><?= htmlspecialchars($c['customerPhone']) ?></td>
                  <td><?= htmlspecialchars($c['created_at'] ?? '—') ?></td>
                  <td>
                    <a href="edit-customer.php?id=<?= $c['id'] ?>"><i class="fas fa-edit"></i></a> |
                    <a href="customers.php?delete_id=<?= $c['id'] ?>" onclick="return confirm('Are you sure you want to delete this customer?');"><i class="fas fa-trash-alt"></i></a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6">No customers found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script>
    // ✅ Add new customer (AJAX)
    document.getElementById('addCustomerForm').addEventListener('submit', function(event) {
      event.preventDefault();
      const formData = new FormData(this);

      fetch('', {
        method: 'POST',
        body: new URLSearchParams(formData)
      })
      .then(res => res.json())
      .then(data => {
        const msg = document.getElementById('formErrorMessage');
        if (data.status === 'success') {
          alert('✅ Customer added successfully!');
          this.reset();

          // Generate Royalty Card
          const name = formData.get('customerName');
          const customerId = 'CUST-' + Math.floor(Math.random() * 1000000);
          document.getElementById('customerNameDisplay').textContent = name;
          document.getElementById('customerIdDisplay').textContent = customerId;

          document.getElementById('royaltyCard').style.display = 'block';
          document.getElementById('qrCode').innerHTML = '';
          new QRCode(document.getElementById("qrCode"), { text: customerId, width: 128, height: 128 });

          // Refresh page to show updated table
          setTimeout(() => location.reload(), 1000);
        } else {
          msg.textContent = data.message;
        }
      })
      .catch(() => alert('Error adding customer.'));
    });
  </script>
</body>
</html>
