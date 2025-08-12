const addstockBtn = document.getElementById('addstockBtn');
const addmultiplestockBtn = document.getElementById('addmultiplestockBtn');
const addproductBtn = document.getElementById('addproductBtn');
const editstockBtn = document.getElementById('edit-stock-btn');
const editproductBtn = document.getElementById('edit-prod-btn');
const unarchiveBtn = document.getElementById('unarchivebtn');
const unarchiveForm = document.getElementById('unarchiveform');

const addproductform = document.getElementById('addproduct');
const addSpecificStockForm = document.getElementById('addspecificstock');
const addmultiplestockform = document.getElementById('addmultiplestockform');
const editStockForm = document.getElementById('edit-stock-form');
const editProductForm = document.getElementById('edit-prod-reset');

// addproductBtn.addEventListener('click', () => {
//     showsweetalert(addproductform);
// });

document.addEventListener('click', function(e) {
    if (e.target.closest('#addproductBtn')) {
        e.preventDefault();
        const form = e.target.closest('#addproduct');
        showsweetalert(form);
    }
});

// addstockBtn.addEventListener('click', () => {
//     showsweetalert(addSpecificStockForm);
// });
document.addEventListener('click', function(e) {
    if (e.target.closest('#addstockBtn')) {
        e.preventDefault();
        const form = e.target.closest('#addspecificstock');
        showsweetalert(form);
    }
});

// addmultiplestockBtn.addEventListener('click', () => {
//     showsweetalert(addmultiplestockform);
// });

document.querySelectorAll('#addmultiplestockBtn').forEach((btn, index) => {
    btn.addEventListener('click', () => {
        const form = document.querySelectorAll('#addmultiplestockform')[index];
        showsweetalert(form);
    });
});

// editstockBtn.addEventListener('click', () => {
//     showsweetalert(editStockForm);
// });

document.addEventListener('click', function(e) {
    if (e.target.closest('#edit-stock-btn')) {
        e.preventDefault();
        const form = e.target.closest('#edit-stock-form');
        showsweetalert(form);
    }
});

// editproductBtn.addEventListener('click', () => {
//     showsweetalert(editProductForm);
// });

document.addEventListener('click', function(e) {
    if (e.target.closest('#edit-prod-btn')) {
        e.preventDefault();
        const form = e.target.closest('#edit-prod-reset');
        showsweetalert(form);
    }
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.unarchivebtn')) {
        e.preventDefault();
        const form = e.target.closest('.unarchiveform');
        showsweetalert(form);
    }
});

function showsweetalert(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, submit it!',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
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
    }
    else if (document.getElementById('errorAlert')) {
        document.getElementById('errorMessage').textContent = errorMessage;
        setTimeout(() => {
            document.getElementById('errorAlert').remove();
        }, 3000);
    }
});
