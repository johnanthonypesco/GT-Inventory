function viewOrder() {
    var modal = document.querySelector('#view-order-modal');
    modal.classList.toggle('hidden');
}
function closevieworder() {
    var modal = document.querySelector('#view-order-modal');
    modal.classList.toggle('hidden');
}

window.onclick = function (event) {
    var modal = document.querySelector('#view-order-modal');
    if (event.target == modal) {
        modal.classList.toggle('hidden');
    }
}