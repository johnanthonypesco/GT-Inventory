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

function showFilters() {
    const motherDiv = document.getElementById('hidden-filters');
    const filterBtnText = document.getElementById('show-filters-btn');
    const dateStartInput = document.getElementById('date-filter-start');
    const dateEndInput = document.getElementById('date-filter-end');
    const companyInput = document.getElementById('company-filter');

    if (motherDiv.classList.contains('hidden')) {
        motherDiv.classList.replace('hidden', 'flex');

        filterBtnText.innerHTML = '<i class="fa-solid fa-close"></i> Disable Search Filters';

        dateStartInput.disabled = false;
        dateEndInput.disabled = false;
        companyInput.disabled = false;
    } else {
        motherDiv.classList.replace('flex', 'hidden');

        filterBtnText.innerHTML = '<i class="fa-solid fa-filter"></i> Enable Search Filters';

        dateStartInput.disabled = true;
        dateEndInput.disabled = true;
        companyInput.disabled = true;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const filterDiv = document.getElementById('hidden-filters');
    const filterBtnText = document.getElementById('show-filters-btn');
    
    const dateStartInput = document.getElementById('date-filter-start');
    const dateEndInput = document.getElementById('date-filter-end');
    const companyInput = document.getElementById('company-filter');

    if (filterDiv.classList.contains("flex")) {
        filterBtnText.innerHTML = '<i class="fa-solid fa-close"></i> Disable Search Filters';

        dateStartInput.disabled = false;
        dateEndInput.disabled = false;
        companyInput.disabled = false;
    }
});