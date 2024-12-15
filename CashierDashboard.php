<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $barcode = $_POST['barcode'];
    $sql = "SELECT * FROM inventory WHERE barcode_no = '$barcode'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product);
    } else {
        echo json_encode(["error" => "Product not found"]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard - Sarasavi Enterprises</title>
    <link rel="stylesheet" href="./styles/cashier.css">
</head>
<body>
    <h1>Sarasavi Enterprises</h1>

    <div class="barcode-search">
        <input type="text" id="barcode" placeholder="Enter barcode">
        <button id="add_to_cart">Add to Cart</button>
    </div>

    <div class="cart-section">
        <table id="cart">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Inventory</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Cart items will be dynamically added here -->
            </tbody>
        </table>

        <div class="cart-summary">
            <h3>Total: <span id="total">0.00</span></h3>
            <button id="print_bill">Print Bill</button>
        </div>
    </div>

    <script>
        const cart = [];

        document.getElementById('add_to_cart').addEventListener('click', () => {
            const barcode = document.getElementById('barcode').value;
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `add_to_cart=true&barcode=${barcode}`
            })
            .then(response => response.json())
            .then(product => {
                if (product.error) {
                    alert(product.error);
                    return;
                }

                // Add product to cart
                const existingItem = cart.find(item => item.barcode === product.barcode_no);
                if (existingItem) {
                    alert('Product already in cart');
                    return;
                }

                const cartItem = {
                    barcode: product.barcode_no,
                    name: product.product_name,
                    price: parseFloat(product.selling_price),
                    inventory: product.quantity,
                    quantity: 1,
                    total: parseFloat(product.selling_price)
                };
                cart.push(cartItem);
                renderCart();

                // Clear barcode input after adding to cart
                document.getElementById('barcode').value = '';
            });
        });

        function renderCart() {
            const tbody = document.querySelector('#cart tbody');
            tbody.innerHTML = '';
            let total = 0;

            cart.forEach((item, index) => {
                const tr = document.createElement('tr');

                tr.innerHTML = `
                    <td>${item.name}</td>
                    <td>${item.price.toFixed(2)}</td>
                    <td>${item.inventory}</td>
                    <td>
                        <input type="number" value="${item.quantity}" min="1" max="${item.inventory}" data-index="${index}" class="quantity-input">
                    </td>
                    <td>${item.total.toFixed(2)}</td>
                    <td>
                        <button class="delete-btn" data-index="${index}">Delete</button>
                    </td>
                `;

                tbody.appendChild(tr);
                total += item.total;
            });

            document.getElementById('total').textContent = total.toFixed(2);

            // Attach event listeners to delete buttons and quantity inputs
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', deleteItem);
            });

            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('input', updateQuantity);
            });
        }

        function deleteItem(event) {
            const index = event.target.dataset.index;
            cart.splice(index, 1);
            renderCart();
        }

        function updateQuantity(event) {
            const index = event.target.dataset.index;
            const quantity = parseInt(event.target.value);
            const item = cart[index];

            if (quantity > item.inventory) {
                alert('Exceeds available stock');
                event.target.value = item.inventory;
                return;
            }

            item.quantity = quantity;
            item.total = item.price * quantity;
            renderCart();
        }

        function generateReceipt() {
            const billNo = Math.floor(Math.random() * 100000); // Generate random bill number
            const dateTime = new Date().toLocaleString(); // Get current date and time

            let receiptContent = `
                <div style="font-family: Arial; font-size: 12px; width: 240px;">
                    <h3 style="text-align: center;">SARASAVI ENTERPRISES</h3>
                    <p style="text-align: center;">215/3, Main Street, Dompe, Sri Lanka</p>
                    <p style="text-align: center;">Date: ${dateTime}</p>
                    <p style="text-align: center;">Bill No: ${billNo}</p>
                    <hr>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="text-align: left;">Item</th>
                                <th style="text-align: right;">Qty</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            let total = 0;
            cart.forEach((item) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                receiptContent += `
                    <tr>
                        <td style="text-align: left;">${item.name}</td>
                        <td style="text-align: right;">${item.quantity}</td>
                        <td style="text-align: right;">LKR ${itemTotal.toFixed(2)}</td>
                    </tr>
                `;
            });

            receiptContent += `
                        </tbody>
                    </table>
                    <hr>
                    <p style="text-align: right; font-weight: bold;">Total: LKR ${total.toFixed(2)}</p>
                    <p style="text-align: center;">Visit us again!</p>
                </div>
            `;

            return receiptContent;
        }

        function printBill() {
            const receiptWindow = window.open("", "Print Receipt", "width=300,height=600");
            receiptWindow.document.write(generateReceipt());
            receiptWindow.document.close();
            receiptWindow.focus();
            receiptWindow.print();
            setTimeout(() => receiptWindow.close(), 1000); // Auto-close after 1 second
        }

        document.getElementById('print_bill').addEventListener('click', printBill);
    </script>
</body>
</html>
