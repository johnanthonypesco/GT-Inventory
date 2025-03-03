function viewOrder(id) {
    var viewOrderModal = document.getElementById("order-modal-" + id);
    viewOrderModal.style.display = "block";
}
function closeOrderModal(id) {
    var viewOrderModal = document.getElementById("order-modal-" + id);
    viewOrderModal.style.display = "none";
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


