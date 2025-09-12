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
                allowOutsideClick: false,
                customClass: {
                    container: 'swal-container',
                    popup: 'swal-popup',
                    title: 'swal-title',
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