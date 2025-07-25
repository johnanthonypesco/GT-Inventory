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

// SIGRAE EMPLOYEE SEARCH SUGGESTION CODES
function isInSuggestionEmployee () {
    const input = document.getElementById('employee_search');
    const dataList = document.getElementById('employee-search-suggestions');
    const options = Array.from(dataList.options).map(option => option.value);

    if (!options.includes(input.value)) {
        alert("Please Choose a Employee & Company From The Search Suggestions.");
        
        return false;
    } else {
        return true;
    }
}
// SIGRAE EMPLOYEE SEARCH SUGGESTION CODES