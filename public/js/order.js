function viewOrder(id) {
    var viewOrderModal = document.getElementById("order-modal-" + id);
    viewOrderModal.classList.replace("hidden", "flex");
}
function closeOrderModal(id) {
    var viewOrderModal = document.getElementById("order-modal-" + id);
    viewOrderModal.classList.replace("flex", "hidden");
}

// window.addEventListener("click", function (e) {
//     var viewOrderModal = document.querySelector(".order-modal");
//     if (e.target == viewOrderModal) {
//         viewOrderModal.style.display = "none";
//     }
// });

function addneworder() {
    var addOrderModal = document.querySelector(".add-new-order-modal");
    addOrderModal.style.display = "block";
}
function closeaddneworder() {
    var addOrderModal = document.querySelector(".add-new-order-modal");
    addOrderModal.style.display = "none";
}


