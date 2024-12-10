<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Budget</title>
  <link rel="stylesheet" href="styles/sidebar.css">
  <link rel="stylesheet" href="styles/topbar.css">
  <link rel="stylesheet" href="styles/budget.css">

  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="inventory.html"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="suppliers.html"><i class="fas fa-truck"></i> Suppliers</a></li>
        <li><a href="budget.html"><i class="fas fa-coins"></i>Budget</a></li>
        <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
        <li><a href="income-costs.html"><i class="fas fa-file-invoice-dollar"></i> Income </a>
        <li><a href="sales.html"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="orders.html" id="ordersMenuItem"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.html"><i class="fas fa-users"></i> Customer Management</a></li>
        <li><a href="shipment.html"><i class="fas fa-shipping-fast"></i> Shipment </a></li>
        <li><a href="purches.html"><i class="fas fa-money-bill-wave"></i> Purches</a></li>
        <li><a href="roles.html" id="rolesMenuItem"><i class="fas fa-user-cog"></i> Role Management</a></li>
      </ul>
      <button id="logout-btn" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <header>
        <div class="top-bar">
          <!-- Add logo image -->
          <div class="logo">
            <img src="assets/logo.jpg" alt="Logo" style="height: 50px;">
          </div>
          <div class="search-bar">
            <input type="text" placeholder="Type for search">
          </div>
          <div class="user-icons">
            <span class="icon"><i class="fas fa-bell"></i></span>
            <span class="icon"><i class="fas fa-comments"></i></span>
            <!-- Wrap the user icon with a link to the profile page -->
            <a href="profile.html">
              <span class="icon"><i class="fas fa-user-circle"></i></span>
            </a>
          </div>
        </div>
      </header>

      <h1>Budget change</h1>
      <p>Here you can manage the budget.</p>


         <!-- Budget Data Table -->
         <table id="budgetTable">
          <thead>
            <tr>
              <th>Budget ID</th>
              <th>Start Date</th>
              <th>Amount</th>
              <th>Budget Rate</th>
              <th>End Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>


          <div class="pagination">
              <button id="prevPage">Previous</button>
              <span id="currentPage">Page 1</span>
              <button id="nextPage">Next</button>
            </div><?php
            // Database connection
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "final_project"; // Change this to your actual database name
            
            $conn = new mysqli($servername, $username, $password, $dbname);
            
            // Check if the connection is successful
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            // Handle delete action
            if (isset($_GET['delete_id'])) {
                $budgetId = $_GET['delete_id'];
                $deleteQuery = "DELETE FROM budget WHERE id = ?";
                $stmt = $conn->prepare($deleteQuery);
                $stmt->bind_param("i", $budgetId);
                if ($stmt->execute()) {
                    echo "<script>alert('Budget deleted successfully'); window.location.href = 'budget.php';</script>";
                } else {
                    echo "Error deleting record: " . $stmt->error;
                }
            }
            
            // Fetch budget data from the database
            $sql = "SELECT * FROM budget";
            $result = $conn->query($sql);
            
            // Close the connection
            $conn->close();
            ?>
            
            <!DOCTYPE html>
            <html lang="en">
            <head>
              <meta charset="UTF-8">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <title>Budget Data</title>
              <link rel="stylesheet" href="styles/sidebar.css">
              <link rel="stylesheet" href="styles/topbar.css">
              <link rel="stylesheet" href="styles/budget.css">
              <!-- Font Awesome for Icons -->
              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            </head>
            <body>
              <div class="container">
                <!-- Sidebar -->
                <aside class="sidebar">
                  <ul>
                    <li><a href="dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="inventory.html"><i class="fas fa-boxes"></i> Inventory</a></li>
                    <li><a href="suppliers.html"><i class="fas fa-truck"></i> Suppliers</a></li>
                    <li><a href="budget.php"><i class="fas fa-coins"></i> Budget</a></li>
                    <li><a href="costs.php"><i class="fas fa-money-bill-wave"></i> Costs</a></li>
                    <li><a href="income-costs.html"><i class="fas fa-file-invoice-dollar"></i> Income</a></li>
                    <li><a href="sales.html"><i class="fas fa-chart-line"></i> Sales</a></li>
                    <li><a href="orders.html"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="customers.html"><i class="fas fa-users"></i> Customer Management</a></li>
                    <li><a href="shipment.html"><i class="fas fa-shipping-fast"></i> Shipment</a></li>
                    <li><a href="purchase.html"><i class="fas fa-money-bill-wave"></i> Purchase</a></li>
                    <li><a href="roles.html"><i class="fas fa-user-cog"></i> Role Management</a></li>
                  </ul>
                  <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log out</button>
                </aside>
            
                <!-- Main Content -->
                <main class="main-content">
                  <!-- Top Bar -->
                  <header>
                    <div class="top-bar">
                      <div class="logo">
                        <img src="assets/logo.jpg" alt="Logo">
                      </div>
                      <div class="search-bar">
                        <input type="text" placeholder="Search Budget Data" id="searchInput">
                      </div>
                      <div class="user-icons">
                        <span class="icon"><i class="fas fa-bell"></i></span>
                        <span class="icon"><i class="fas fa-comments"></i></span>
                        <a href="profile.html">
                          <span class="icon"><i class="fas fa-user-circle"></i></span>
                        </a>
                      </div>
                    </div>
                  </header>
            
                  <h1>Budget Data</h1>
            
                  <div class="table-header">
                    <a href="budgetForm.php">
                      <button id="addBudgetBtn">Add New Budget Entry</button>
                    </a>
                  </div>
            
                  <!-- Budget Data Table -->
                  <table id="budgetTable">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Start Date</th>
                        <th>Amount</th>
                        <th>Budget Rate</th>
                        <th>End Date</th>
                        <th>Created At</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($result->num_rows > 0) {
                          while($row = $result->fetch_assoc()) {
                              echo "<tr>";
                              echo "<td>" . $row['id'] . "</td>";
                              echo "<td>" . $row['start_date'] . "</td>";
                              echo "<td>" . $row['amount'] . "</td>";
                              echo "<td>" . $row['budget_rate'] . "</td>";
                              echo "<td>" . $row['end_date'] . "</td>";
                              echo "<td>" . $row['created_at'] . "</td>";
                              echo "<td>
                                      <a href='edit_budget.php?id=" . $row['id'] . "'><i class='fas fa-edit'></i> </a> | 
                                      <a href='?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete?\");'><i class='fas fa-trash-alt'></i> </a>
                                    </td>";
                              echo "</tr>";
                          }
                      } else {
                          echo "<tr><td colspan='7'>No records found</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
            
                  <!-- Pagination Controls -->
                  <div class="pagination">
                    <button id="prevPage">Previous</button>
                    <span id="currentPage">Page 1</span>
                    <button id="nextPage">Next</button>
                  </div>
                </main>
              </div>
            
            </body>
            </html> 
            




















    </main>
  </div>

  <script>

    // JavaScript to handle logout redirection
    document.getElementById('logout-btn').addEventListener('click', function() {
      window.location.href = 'index.html'; // Redirect to index.html
    });
  </script>
</body>
</html>
