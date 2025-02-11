var viewOrder = document.getElementById("view-order");
var orderModal = document.getElementById("order-modal");
var modalClose = document.getElementById("modal-close");

viewOrder.addEventListener("click", () => {
    orderModal.style.display = "block";
});

modalClose.addEventListener("click", () => {
    orderModal.style.display = "none";
});

window.addEventListener("click", (e) => {
    if (e.target == orderModal) {
        orderModal.style.display = "none";
    }
});
