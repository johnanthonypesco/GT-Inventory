document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll(".product-form");
    const orderSummary = document.getElementById("order-summary");
    const subtotalElement = document.getElementById("subtotal");
    let subtotal = 0;

    forms.forEach(form => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const productName = form.querySelector(".product-name").textContent;
            const price = parseInt(form.querySelector(".product-price").textContent.replace("₱", ""));
            const quantity = parseInt(form.querySelector(".quantity").value);
            const total = price * quantity;

            let existingOrder = [...orderSummary.children].find(item => 
                item.dataset.productName === productName
            );

            if (existingOrder) {
                let existingQuantity = parseInt(existingOrder.dataset.quantity);
                let newQuantity = existingQuantity + quantity;
                let newTotal = price * newQuantity;

                existingOrder.dataset.quantity = newQuantity;
                existingOrder.querySelector(".order-quantity").textContent = `${productName} x ${newQuantity}`;
                existingOrder.querySelector(".order-price").textContent = `Price: ₱${newTotal}`;

                subtotal += total;
            } else {
                const orderItem = document.createElement("div");
                orderItem.classList.add("flex", "justify-between", "mt-2");
                orderItem.dataset.productName = productName;
                orderItem.dataset.quantity = quantity;

                orderItem.innerHTML = `
                    <h1 class="text-sm font-semibold w-[60%] order-quantity">${productName} x ${quantity}</h1>
                    <p class="text-sm font-semibold order-price">Price: ₱${total}</p>
                `;

                orderSummary.appendChild(orderItem);

                subtotal += total;
            }

            subtotalElement.textContent = `₱${subtotal}`;
        });
    });
});
