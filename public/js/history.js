function viewOrder(){
    var orderModal = document.getElementById("order-modal");
    orderModal.style.display = "block";
}

function closeOrderModal(){
    var orderModal = document.getElementById("order-modal");
    orderModal.style.display = "none";
}

window.addEventListener("click", function(event){
    var orderModal = document.getElementById("order-modal");
    if(event.target == orderModal){
        orderModal.style.display = "none";
    }
});