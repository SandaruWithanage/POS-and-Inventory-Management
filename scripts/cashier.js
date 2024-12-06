const inventory = [
    { id: 1, name: "Product A", price: 100.0, barcode: "123456789012" },
    { id: 2, name: "Product B", price: 25.5, barcode: "123456789013" },
    { id: 3, name: "Product C", price: 300.0, barcode: "123456789014" },
  ];
  
  const cart = [];
  const cartTableBody = document.querySelector("#cart-table tbody");
  const totalPriceEl = document.getElementById("total-price");
  const barcodeInput = document.getElementById("barcode-input");
  const addToCartBtn = document.getElementById("add-to-cart-btn");
  const checkoutBtn = document.getElementById("checkout-btn");
  const paymentSection = document.getElementById("payment-section");
  const paymentTotalEl = document.getElementById("payment-total");
  const paidAmountEl = document.getElementById("paid-amount");
  const changeAmountEl = document.getElementById("change-amount");
  const cashBtn = document.getElementById("cash-btn");
  const cardBtn = document.getElementById("card-btn");
  const paymentInfo = document.getElementById("payment-info");
  const paymentConfirmation = document.getElementById("payment-confirmation");
  const printReceiptBtn = document.getElementById("print-receipt-btn");
  
  function updateCartTable() {
    cartTableBody.innerHTML = "";
    let total = 0;
  
    cart.forEach((item, index) => {
      total += item.price * item.quantity;
  
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${item.name}</td>
        <td>${item.price.toFixed(2)}</td>
        <td>
          <input type="number" value="${item.quantity}" min="1" onchange="updateQuantity(${index}, this.value)">
        </td>
        <td>${(item.price * item.quantity).toFixed(2)}</td>
        <td><button onclick="removeFromCart(${index})">Remove</button></td>
      `;
      cartTableBody.appendChild(row);
    });
  
    totalPriceEl.textContent = total.toFixed(2);
  }
  
  function addToCart(product) {
    const existingProduct = cart.find((item) => item.barcode === product.barcode);
    if (existingProduct) {
      existingProduct.quantity += 1;
    } else {
      cart.push({ ...product, quantity: 1 });
    }
    updateCartTable();
  }
  
  function updateQuantity(index, quantity) {
    cart[index].quantity = parseInt(quantity, 10);
    updateCartTable();
  }
  
  function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartTable();
  }
  
  addToCartBtn.addEventListener("click", () => {
    const barcode = barcodeInput.value.trim();
    const product = inventory.find((item) => item.barcode === barcode);
    if (product) {
      addToCart(product);
      barcodeInput.value = "";
    } else {
      alert("Product not found!");
    }
  });
  
  checkoutBtn.addEventListener("click", () => {
    const total = parseFloat(totalPriceEl.textContent);
    paymentTotalEl.textContent = total.toFixed(2);
    paymentSection.classList.remove("hidden");
  });
  
  paidAmountEl.addEventListener("input", () => {
    const total = parseFloat(paymentTotalEl.textContent);
    const paidAmount = parseFloat(paidAmountEl.value) || 0;
    const change = paidAmount - total;
    changeAmountEl.textContent = change.toFixed(2);
  });
  
  cashBtn.addEventListener("click", () => {
    paymentInfo.classList.remove("hidden");
    paymentConfirmation.textContent = "Payment completed via Cash.";
  });
  
  cardBtn.addEventListener("click", () => {
    paymentInfo.classList.remove("hidden");
    paymentConfirmation.textContent = "Payment completed via Card.";
  });
  
  printReceiptBtn.addEventListener("click", () => {
    const receiptWindow = window.open("", "Print Receipt", "width=300,height=600");
    receiptWindow.document.write(generateReceipt());
    receiptWindow.document.close();
    receiptWindow.print();
  });
  
  function generateReceipt() {
    let receipt = `<h3>POS Receipt</h3>`;
    receipt += `<table>`;
    cart.forEach((item) => {
      receipt += `
        <tr>
          <td>${item.name}</td>
          <td>${item.quantity}</td>
          <td>${(item.price * item.quantity).toFixed(2)}</td>
        </tr>
      `;
    });
    receipt += `</table>`;
    receipt += `<p>Total: ${totalPriceEl.textContent}</p>`;
    receipt += `<p>Paid: ${paidAmountEl.value}</p>`;
    receipt += `<p>Change: ${changeAmountEl.textContent}</p>`;
    return receipt;
  }
  