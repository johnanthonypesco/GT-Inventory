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

// SIGRAE EMPLOYEE SEARCH SUGGESTION CODES
function isInSuggestionDeal () {
    const input = document.getElementById('deal_search');
    const dataList = document.getElementById('deal-search-suggestions');
    const options = Array.from(dataList.options).map(option => option.value);

    if (!options.includes(input.value)) {
        alert("Please Choose a Product Deal From The Search Suggestions.");
        
        return false;
    } else {
        return true;
    }
}
// SIGRAE EMPLOYEE SEARCH SUGGESTION CODES