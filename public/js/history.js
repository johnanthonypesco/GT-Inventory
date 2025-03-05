function viewOrder(id) {
    var viewOrderModal = document.getElementById("view-order-modal-" + id);
    viewOrderModal.style.display = "block";
}
function closeOrderModal(id) {
    var viewOrderModal = document.getElementById("view-order-modal-" + id);
    viewOrderModal.style.display = "none";
}

window.addEventListener("click", function(event){
    var orderModal = document.getElementById("order-modal");
    if(event.target == orderModal){
        orderModal.style.display = "none";
    }
});