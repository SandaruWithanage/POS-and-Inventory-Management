<?php
// Database connection settings
$servername = "localhost";
$username = "root";  // Change this to your database username
$password = "";      // Change this to your database password
$dbname = "final_project";  // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get category filter from GET parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Query to fetch categories (for filter options)
$categoryQuery = "SELECT DISTINCT category FROM inventory";
$categoryResult = $conn->query($categoryQuery);

// Query to fetch products based on category
$sql = "SELECT * FROM inventory WHERE stock_status = 'In Stock'";

// Apply category filter if selected
if ($category != '') {
    $sql .= " AND category = '$category'";
}

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop - E-commerce</title>
  <link rel="stylesheet" href="styles/shop.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar for Category Filters -->
    <aside class="sidebar">
      <h3>Categories</h3>
      <ul>
        <li><a href="shop.php">View All Products</a></li>
        <?php
        // Output category filters
        if ($categoryResult->num_rows > 0) {
            while ($catRow = $categoryResult->fetch_assoc()) {
                $categoryName = $catRow['category'];
                echo "<li><a href='shop.php?category=$categoryName'>$categoryName</a></li>";
            }
        }
        ?>
      </ul>
    </aside>

    <!-- Main content area -->
    <main class="main-content">
      <header>
        <div class="top-bar">
          <div class="logo">
            <img src="assets/logo.jpg" alt="Logo" style="height: 50px;">
          </div>
          <div class="search-bar">
            <input type="text" placeholder="Search products...">
          </div>
        </div>
      </header>

      <h1>Sarasavi Textiles</h1>

      <div class="product-list">
        <?php
        // Check if products are available
        if ($result->num_rows > 0) {
            // Output each product as a card
            while ($row = $result->fetch_assoc()) {
                $productName = $row['product_name'];
                $productImage = 'http://localhost/final-project/' . $row['product_image'];
                $unitPrice = number_format($row['unit_price'], 2);
                $productId = $row['id']; // Assuming 'id' is the primary key of the product

                echo "
                <div class='product-card'>
                    <img src='$productImage' alt='$productName' class='product-image'>
                    <h3 class='product-name'>$productName</h3>
                    <p class='product-price'>\$ $unitPrice</p>
                    <a href='product-detail.php?id=$productId' class='view-details-btn'>View Details</a>
                </div>";
            }
        } else {
            echo "<p>No products available.</p>";
        }

        // Close the database connection
        $conn->close();
        ?>
      </div>

    </main>
  </div>

  <script>
    // You can add JavaScript here for handling search functionality or other interactivity
  </script>
</body>
</html>
