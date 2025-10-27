<?php
// ============================
// Database connection
// ============================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ============================
// Handle Add to Cart (AJAX)
// ============================
if (isset($_POST['add_to_cart'])) {
    $barcode = $_POST['barcode'];
    $sql = "SELECT * FROM inventory WHERE barcode_no = '$barcode' AND quantity > 0";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product);
    } else {
        echo json_encode(["error" => "Product not found or out of stock"]);
    }
    exit;
}

// ============================
// Fetch Customers (for selection)
// ============================
$customers = [];
$customerQuery = "SELECT id, customerName, customerEmail FROM customers ORDER BY customerName ASC";
$result = $conn->query($customerQuery);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

// ============================
// Fetch Products (for search dropdown)
// ============================
$products = [];
$productQuery = "SELECT product_name, barcode_no FROM inventory WHERE quantity > 0 ORDER BY product_name ASC";
$result = $conn->query($productQuery);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
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

    <!-- Top Controls -->
    <div class="top-actions">
        <button onclick="window.location.href='customers.php'" class="add-customer-btn">➕ Add Customer</button>

        <select id="customerSelect">
            <option value="">Select Customer</option>
            <?php foreach ($customers as $c): ?>
                <option value="<?= htmlspecialchars($c['id']) ?>"><?= htmlspecialchars($c['customerName']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>
            <input type="checkbox" id="royaltyCheck"> Royalty Customer (5% Discount)
        </label>
    </div>

    <!-- Product Search and Barcode -->
    <div class="barcode-search">
        <select id="productSelect">
            <option value="">-- Search or Choose Product --</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= htmlspecialchars($p['barcode_no']) ?>">
                    <?= htmlspecialchars($p['product_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="text" id="barcode" placeholder="Enter barcode">
        <button id="add_to_cart">Add to Cart</button>
    </div>

    <!-- Cart -->
    <div class="cart-section">
        <table id="cart">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price (LKR)</th>
                    <th>Inventory</th>
                    <th>Quantity</th>
                    <th>Product Discount (%)</th>
                    <th>Total (LKR)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="cart-summary">
            <h3>Subtotal: <span id="subtotal">0.00</span></h3>
            <h3>Discount: <span id="discount">0.00</span></h3>
            <h3><b>Grand Total: <span id="total">0.00</span></b></h3>
            <button id="print_bill">🧾 Print Bill</button>
        </div>
    </div>

<script>
    const cart = [];
    let royaltyDiscount = 0;

    // Link product dropdown to barcode field
    document.getElementById('productSelect').addEventListener('change', function() {
        const barcode = this.value;
        document.getElementById('barcode').value = barcode;
    });

    document.getElementById('royaltyCheck').addEventListener('change', function() {
        royaltyDiscount = this.checked ? 5 : 0;
        renderCart();
    });

    document.getElementById('add_to_cart').addEventListener('click', () => {
        const barcode = document.getElementById('barcode').value.trim();
        if (!barcode) {
            alert('Please enter or select a product.');
            return;
        }

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

            const existingItem = cart.find(item => item.barcode === product.barcode_no);
            if (existingItem) {
                alert('Product already in cart.');
                return;
            }

            const cartItem = {
                barcode: product.barcode_no,
                name: product.product_name,
                price: parseFloat(product.selling_price),
                inventory: product.quantity,
                quantity: 1,
                discount: 0,
                total: parseFloat(product.selling_price)
            };
            cart.push(cartItem);
            renderCart();

            document.getElementById('barcode').value = '';
            document.getElementById('productSelect').value = '';
        });
    });

    function renderCart() {
        const tbody = document.querySelector('#cart tbody');
        tbody.innerHTML = '';
        let subtotal = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity * (1 - item.discount / 100);
            subtotal += itemTotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.name}</td>
                <td>${item.price.toFixed(2)}</td>
                <td>${item.inventory}</td>
                <td><input type="number" value="${item.quantity}" min="1" max="${item.inventory}" data-index="${index}" class="quantity-input"></td>
                <td><input type="number" value="${item.discount}" min="0" max="100" data-index="${index}" class="discount-input"></td>
                <td>${itemTotal.toFixed(2)}</td>
                <td><button class="delete-btn" data-index="${index}">Delete</button></td>
            `;
            tbody.appendChild(tr);
        });

        const royaltyAmount = subtotal * (royaltyDiscount / 100);
        const grandTotal = subtotal - royaltyAmount;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('discount').textContent = royaltyAmount.toFixed(2);
        document.getElementById('total').textContent = grandTotal.toFixed(2);

        document.querySelectorAll('.delete-btn').forEach(btn => btn.addEventListener('click', deleteItem));
        document.querySelectorAll('.quantity-input').forEach(inp => inp.addEventListener('input', updateQuantity));
        document.querySelectorAll('.discount-input').forEach(inp => inp.addEventListener('input', updateDiscount));
    }

    function deleteItem(e) {
        const index = e.target.dataset.index;
        cart.splice(index, 1);
        renderCart();
    }

    function updateQuantity(e) {
        const index = e.target.dataset.index;
        const quantity = parseInt(e.target.value);
        const item = cart[index];
        if (quantity > item.inventory) {
            alert('Exceeds available stock');
            e.target.value = item.inventory;
            return;
        }
        item.quantity = quantity;
        renderCart();
    }

    function updateDiscount(e) {
        const index = e.target.dataset.index;
        const discount = parseFloat(e.target.value);
        const item = cart[index];
        if (discount < 0 || discount > 100) {
            alert('Invalid discount value');
            e.target.value = item.discount;
            return;
        }
        item.discount = discount;
        renderCart();
    }

    function generateReceipt() {
        const billNo = Math.floor(Math.random() * 100000);
        const dateTime = new Date().toLocaleString();
        const customer = document.getElementById('customerSelect').selectedOptions[0]?.text || "Guest";
        const isRoyalty = document.getElementById('royaltyCheck').checked ? "Yes" : "No";

        let receiptContent = `
            <div style="font-family: Arial; font-size: 12px; width: 240px;">
                <h3 style="text-align: center;">SARASAVI ENTERPRISES</h3>
                <p style="text-align: center;">215/3, Main Street, Dompe, Sri Lanka</p>
                <p style="text-align: center;">Date: ${dateTime}</p>
                <p style="text-align: center;">Bill No: ${billNo}</p>
                <p style="text-align: center;">Customer: ${customer}</p>
                <p style="text-align: center;">Royalty: ${isRoyalty}</p>
                <hr>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead><tr><th style="text-align:left;">Item</th><th style="text-align:right;">Qty</th><th style="text-align:right;">Total</th></tr></thead>
                    <tbody>
        `;

        let subtotal = 0;
        cart.forEach((item) => {
            const itemTotal = item.price * item.quantity * (1 - item.discount / 100);
            subtotal += itemTotal;
            receiptContent += `
                <tr>
                    <td>${item.name}</td>
                    <td style="text-align:right;">${item.quantity}</td>
                    <td style="text-align:right;">LKR ${itemTotal.toFixed(2)}</td>
                </tr>
            `;
        });

        const royaltyAmt = subtotal * (royaltyDiscount / 100);
        const grandTotal = subtotal - royaltyAmt;

        receiptContent += `
                    </tbody>
                </table>
                <hr>
                <p style="text-align:right;">Subtotal: LKR ${subtotal.toFixed(2)}</p>
                <p style="text-align:right;">Royalty Discount: LKR ${royaltyAmt.toFixed(2)}</p>
                <p style="text-align:right;font-weight:bold;">Grand Total: LKR ${grandTotal.toFixed(2)}</p>
                <p style="text-align:center;">Thank you! Visit us again.</p>
            </div>
        `;
        return receiptContent;
    }

    function printBill() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }
        const receiptWindow = window.open("", "Print Receipt", "width=300,height=600");
        receiptWindow.document.write(generateReceipt());
        receiptWindow.document.close();
        receiptWindow.focus();
        receiptWindow.print();
        setTimeout(() => receiptWindow.close(), 1000);
    }

    document.getElementById('print_bill').addEventListener('click', printBill);
</script>
</body>
</html>
