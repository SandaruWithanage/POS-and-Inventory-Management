<?php
// Start the session if needed
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Reports Dashboard</title>
  <link rel="stylesheet" href="../styles/dashboard.css">
  <link rel="stylesheet" href="../styles/topbar.css">
  <link rel="stylesheet" href="../styles/sidebar.css">
  <style>
  /* Dashboard Cards Grid Style */
  .dashboard-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3 cards per row */
    gap: 20px;
    margin: 20px;
  }

  .card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 50px;
    text-align: center;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  }

  .card-icon {
    font-size: 40px;
    margin-bottom: 10px;
    color: #4CAF50;
  }

  h3 {
    font-size: 18px;
    margin: 0;
    color: #333;
  }
  </style>

  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- Page Fade Animation -->
  <style>
    body {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.6s ease, transform 0.6s ease;
    }
    body.loaded {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>
<body>
  <div class="container">
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
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="roles.php"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header>
        <div class="top-bar">
          <div class="logo">
            <img src="../assets/logo.jpg" alt="Logo" style="height: 50px;">
          </div>
          <div class="search-bar">
            <input type="text" placeholder="Type for search">
          </div>
          <div class="user-icons">
            <span class="icon"><i class="fas fa-bell"></i></span>
            <span class="icon"><i class="fas fa-comments"></i></span>
            <a href="profile.php">
              <span class="icon"><i class="fas fa-user-circle"></i></span>
            </a>
          </div>
        </div>
      </header>

      <h1>Reports Dashboard</h1>

      <section class="dashboard-cards">
        <!-- Financial Report -->
        <a href="financialReport.php" class="card">
          <i class="fas fa-coins card-icon"></i>
          <h3>Financial Report</h3>
        </a>

        <!-- Sales Report -->
        <a href="salesReport.php" class="card">
          <i class="fas fa-chart-line card-icon"></i>
          <h3>Sales Report</h3>
        </a>

        <!-- Product (Inventory) Report -->
        <a href="productReport.php" class="card">
          <i class="fas fa-box-open card-icon"></i>
          <h3>Product Report</h3>
        </a>

        <!-- Procurement Report -->
        <a href="procurementReport.php" class="card">
          <i class="fas fa-shopping-basket card-icon"></i>
          <h3>Procurement Report</h3>
        </a>

        <!-- Supplier Report -->
        <a href="supplierReport.php" class="card">
          <i class="fas fa-truck card-icon"></i>
          <h3>Supplier Report</h3>
        </a>

        <!-- Revenue Report -->
        <a href="revenueReport.php" class="card">
          <i class="fas fa-hand-holding-usd card-icon"></i>
          <h3>Revenue Report</h3>
        </a>
      </section>
    </main>
  </div>

  <script>
    // Smooth fade-in transition on load
    window.addEventListener("load", () => {
      document.body.classList.add("loaded");
    });
  </script>
</body>
</html>
