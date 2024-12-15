<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the cart data from POST
$cart = json_decode($_POST['cart'], true);

// Loop through each item in the cart and update the product quantity
foreach ($cart as $item) {
    $productId = $item['id'];
    $quantity = $item['quantity'];

    // Update the quantity in the database
    $sql = "UPDATE products SET quantity = quantity - $quantity WHERE id = $productId";

    if ($conn->query($sql) !== TRUE) {
        echo "Error updating record: " . $conn->error;
        exit();
    }
}

// Close the connection
$conn->close();

echo "Success";
?>
