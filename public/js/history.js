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

function toggleFilterState(state) {
    const filters = [
        document.getElementById('date-filter-start'),
        document.getElementById('date-filter-end'),
        document.getElementById('company-filter'),
        document.getElementById('product-filter'),
        document.getElementById('po-filter'),
    ];

    if (state === "enabled") {
        filters.forEach(filter => {
            filter.disabled = false;
        });
    }

    else if (state === "disabled") {
        filters.forEach(filter => {
            filter.disabled = true;
        });
    }
}

function toggleModal(show) {
    const modal = document.getElementById('filter-modal');

    if (show) {
        modal.classList.replace('hidden', 'flex');

        toggleFilterState('enabled');
    } else {
        modal.classList.replace('flex', 'hidden');        
    }
}



document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById('filter-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const showFiltersBtn = document.getElementById('show-filters-btn');
    const filterState = document.getElementById('filter-state');

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

    if (filterState.dataset.state === 'used') {
        toggleFilterState('enabled');
    }

    // window.addEventListener('click', (event) => {
    //     if (event.target === modal) {
    //         toggleModal(false);
    //     }
    // });
});