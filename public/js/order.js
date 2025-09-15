function companyChosen() {
    const company = document.getElementById('company-select');
    const hiddenInputsDiv = document.getElementById('create-order-hidden-inputs');

    hiddenInputsDiv.className = 'flex flex-col gap-3';

    const userInput = document.querySelector('[name="user_id"]');
    const dealsInput = document.querySelector('[name="exclusive_deal_id"]');

    userInput.setAttribute('list', `create-suggestions-${company.value}`);
    dealsInput.setAttribute('list', `available-deals-${company.value}`);
}

document.addEventListener('click', function(e) {
    const btn = e.target.closest('#add-new-order-submit');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('#add-new-order-form');
    if(form) showsweetalert(form);
});


function showsweetalert(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action can't be undone. Please confirm if you want to proceed.",
        icon: 'info',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false,
        customClass: {
            container: 'swal-container',
            popup: 'swal-popup',
            title: 'swal-title',
            htmlContainer: 'swal-content', 
            confirmButton: 'swal-confirm-button',
            cancelButton: 'swal-cancel-button',
            icon: 'swal-icon'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                text: "Please wait, your request is being processed.",
                allowOutsideClick: false,
                customClass: {
                    container: 'swal-container',
                    popup: 'swal-popup',
                    title: 'swal-title',
                    htmlContainer: 'swal-content', 
                    confirmButton: 'swal-confirm-button'
                },
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            form.submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const successMessage = window.successMessage;
    const errorMessage = window.errorMessage;

    if (document.getElementById('successAlert')) {
        document.getElementById('successMessage').textContent = successMessage;
        setTimeout(() => {
            document.getElementById('successAlert').remove();
        }, 3000);
    } else if (document.getElementById('errorAlert')) {
        document.getElementById('errorMessage').textContent = errorMessage;
        setTimeout(() => {
            document.getElementById('errorAlert').remove();
        }, 3000);
    }
});

// filter logic
function toggleFilterState(state) {
    const filters = [
        document.getElementById('date-filter-start'),
        document.getElementById('date-filter-end'),
        document.getElementById('company-filter'),
        document.getElementById('product-filter'),
        document.getElementById('po-filter'),
        document.getElementById('status-filter'),
        document.getElementById('province-filter'),
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