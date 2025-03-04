function viewOrder(id) {
    var modal = document.getElementById('view-order-modal-' + id);
    modal.classList.toggle('hidden');
}
function closevieworder(id) {
    var modal = document.getElementById('view-order-modal-' + id);
    modal.classList.toggle('hidden');
}

window.onclick = function (event) {
    var modal = document.querySelector('#view-order-modal');
    if (event.target == modal) {
        modal.classList.toggle('hidden');
    }
}