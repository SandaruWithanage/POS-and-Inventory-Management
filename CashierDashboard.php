<?php
// Start the session (optional, if you need session-based authentication)
session_start();

// Database connection
$servername = "localhost";  // Change to your database server
$username = "root";         // Change to your database username
$password = "";             // Change to your database password
$dbname = "final_project";     // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database
$query = "SELECT * FROM products";
$result = $conn->query($query);
$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarasavi Enterprices POS System</title>
    <link rel="stylesheet" href="./styles/cashier.css">
</head>
<body>
    <div class="dashboard">
        <header>
            <h1>Sarasavi Enterprices POS System</h1>
        </header>

        <main>
            <section class="input-section">
                <label for="barcode-input">Scan or Enter Barcode:</label>
                <input type="text" id="barcode-input" placeholder="Enter barcode" autofocus>
                <button id="add-to-cart-btn">Add to Cart</button>
            </section>

            <section class="cart-section">
                <h2>Cart</h2>
                <table id="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="total-section">
                    <p>Total: <span id="total-price">0.00</span></p>
                    <p>Tax: <span id="tax-amount">0.00</span></p>
                </div>
                <button id="checkout-btn">Checkout</button>
            </section>

            <section class="cart-section">
                <button id="print-bill-btn">Print Bill</button>
            </section>
        </main>
    </div>

    <script>
      // Fetching the products from the PHP code
      const inventory = <?php echo json_encode($products); ?>; // PHP products array converted to JavaScript
      let cart = JSON.parse(localStorage.getItem('cart')) || [];
      const cartTableBody = document.querySelector("#cart-table tbody");
      const totalPriceEl = document.getElementById("total-price");
      const taxAmountEl = document.getElementById("tax-amount");
      const barcodeInput = document.getElementById("barcode-input");
      const addToCartBtn = document.getElementById("add-to-cart-btn");

      function updateCartTable() {
          cartTableBody.innerHTML = "";
          let total = 0;

          cart.forEach((item, index) => {
              const row = document.createElement("tr");

              row.innerHTML = `
                  <td>${item.name}</td>
                  <td>${item.price.toFixed(2)}</td>
                  <td>
                      <button onclick="updateQuantity(${index}, -1)">-</button>
                      ${item.quantity}
                      <button onclick="updateQuantity(${index}, 1)">+</button>
                  </td>
                  <td>${(item.price * item.quantity).toFixed(2)}</td>
                  <td><button onclick="removeFromCart(${index})">Remove</button></td>
              `;

              cartTableBody.appendChild(row);
              total += item.price * item.quantity;
          });

          let tax = total * 0.10; // Tax = 10% of total price
          total += tax;

          totalPriceEl.textContent = total.toFixed(2);
          taxAmountEl.textContent = tax.toFixed(2);
      }

      function addToCart(product) {
          const existingProduct = cart.find((item) => item.barcode_no === product.barcode_no);

          if (existingProduct) {
              existingProduct.quantity += 1;
          } else {
              cart.push({ ...product, quantity: 1 });
          }

          console.log(cart);  // Debugging line to check cart data
          updateCartTable();
          localStorage.setItem('cart', JSON.stringify(cart)); // Persist cart data
      }

      function removeFromCart(index) {
          cart.splice(index, 1);
          updateCartTable();
          localStorage.setItem('cart', JSON.stringify(cart)); // Persist cart data
      }

      function updateQuantity(index, change) {
          const item = cart[index];
          if (item.quantity + change > 0) {
              item.quantity += change;
              updateCartTable();
              localStorage.setItem('cart', JSON.stringify(cart)); // Persist cart data
          }
      }

      function generateReceipt() {
          let receiptContent = `
              <div style="font-family: Arial; font-size: 12px; width: 240px;">
                  <h3 style="text-align: center;">POS System</h3>
                  <p style="text-align: center;">Thank you for shopping!</p>
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
                      <td style="text-align: right;">${itemTotal.toFixed(2)}</td>
                  </tr>
              `;
          });

          receiptContent += `
                      </tbody>
                  </table>
                  <hr>
                  <p style="text-align: right; font-weight: bold;">Total: $${total.toFixed(2)}</p>
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

      function checkout() {
          const cartData = cart.map(item => ({
              id: item.id,
              quantity: item.quantity,
          }));

          const xhr = new XMLHttpRequest();
          xhr.open("POST", "update_quantity.php", true);
          xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          xhr.onload = function () {
              if (xhr.status === 200) {
                  alert("Checkout successful!");
                  cart = []; // Clear the cart
                  localStorage.removeItem('cart'); // Remove cart data from localStorage
                  updateCartTable(); // Update the cart table
              } else {
                  alert("Error processing checkout.");
              }
          };
          xhr.send("cart=" + JSON.stringify(cartData)); // Send the cart data as JSON
      }

      addToCartBtn.addEventListener("click", () => {
          const barcode = barcodeInput.value.trim();
          console.log(barcode);  // Debugging line
          const product = inventory.find((item) => item.barcode_no === barcode);

          if (product) {
              addToCart(product);
              barcodeInput.value = "";
          } else {
              alert("Product not found!");
          }
      });

      document.getElementById("checkout-btn").addEventListener("click", checkout);
      document.getElementById("print-bill-btn").addEventListener("click", printBill);

      updateCartTable();
    </script>
</body>
</html>
