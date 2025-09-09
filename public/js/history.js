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

function toggleModal(show) {
    const modal = document.getElementById('filter-modal');
    const filterBtnText = document.getElementById('show-filters-btn');
    const dateStartInput = document.getElementById('date-filter-start');
    const dateEndInput = document.getElementById('date-filter-end');
    const companyInput = document.getElementById('company-filter');

    if (show) {
        modal.classList.replace('hidden', 'flex');
        dateStartInput.disabled = false;
        dateEndInput.disabled = false;
        companyInput.disabled = false;
    } else {
        modal.classList.replace('flex', 'hidden');
        dateStartInput.disabled = true;
        dateEndInput.disabled = true;
        companyInput.disabled = true;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById('filter-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const showFiltersBtn = document.getElementById('show-filters-btn');

    if (modal.classList.contains("flex")) {
        toggleModal(false);
    }

    if (showFiltersBtn) {
        showFiltersBtn.addEventListener('click', () => {
            toggleModal(modal.classList.contains('hidden'));
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => {
            toggleModal(false);
        });
    }

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            toggleModal(false);
        }
    });
});