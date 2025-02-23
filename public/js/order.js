function viewOrder() {
    var viewOrderModal = document.querySelector(".order-modal");
    viewOrderModal.style.display = "block";
}
function closeOrderModal() {
    var viewOrderModal = document.querySelector(".order-modal");
    viewOrderModal.style.display = "none";
}

window.addEventListener("click", function (e) {
    var viewOrderModal = document.querySelector(".order-modal");
    if (e.target == viewOrderModal) {
        viewOrderModal.style.display = "none";
    }
});

function addneworder() {
    var addOrderModal = document.querySelector(".add-new-order-modal");
    addOrderModal.style.display = "block";
}
function closeaddneworder() {
    var addOrderModal = document.querySelector(".add-new-order-modal");
    addOrderModal.style.display = "none";
}


