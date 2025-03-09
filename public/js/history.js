function viewOrder(id) {
    var viewOrderModal = document.getElementById("order-modal-" + id);
    viewOrderModal.classList.replace("hidden", "flex");
}
function closeOrderModal(id) {
    var viewOrderModal = document.getElementById("order-modal-" + id);
    viewOrderModal.classList.replace("flex", "hidden");
}

window.addEventListener("click", function(event){
    var orderModal = document.getElementById("order-modal");
    if(event.target == orderModal){
        orderModal.style.display = "none";
    }
});