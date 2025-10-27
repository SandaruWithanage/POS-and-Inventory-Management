<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed."]));
}

$supplierId = isset($_GET['supplier_id']) ? intval($_GET['supplier_id']) : 0;
$response = [];

if ($supplierId > 0) {
    $query = "SELECT productSupplied FROM suppliers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $supplierId);
    $stmt->execute();
    $stmt->bind_result($productSupplied);
    $stmt->fetch();
    $stmt->close();

    // Convert comma-separated products to array
    $products = array_map('trim', explode(',', $productSupplied));

    // Define known categories for matching
    $electronics = ["Televisions", "Laptops", "Mobile Phones", "Refrigerators", "Washing Machines"];
    $furniture = ["Sofas", "Beds", "Dining Tables", "Chairs", "Cabinets", "Wardrobes", "Office Desks"];

    // Identify which categories belong to the supplier
    $availableCategories = [];
    foreach ($products as $product) {
        if (in_array($product, $electronics)) {
            $availableCategories["📺 Electronics"][] = $product;
        } elseif (in_array($product, $furniture)) {
            $availableCategories["🪑 Furniture"][] = $product;
        }
    }

    $response = [
        "categories" => $availableCategories,
        "products" => $products
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>
