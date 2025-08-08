const addstockBtn = document.getElementById('addstockBtn');
const addmultiplestockBtn = document.getElementById('addmultiplestockBtn');
const addproductBtn = document.getElementById('addproductBtn');
const editstockBtn = document.getElementById('edit-stock-btn');
const editproductBtn = document.getElementById('edit-prod-btn');

const addproductform = document.getElementById('addproduct');
const addSpecificStockForm = document.getElementById('addspecificstock');
const addmultiplestockform = document.getElementById('addmultiplestockform');
const editStockForm = document.getElementById('edit-stock-form');
const editProductForm = document.getElementById('edit-prod-reset');

addproductBtn.addEventListener('click', () => {
    showsweetalert(addproductform);
});

addstockBtn.addEventListener('click', () => {
    showsweetalert(addSpecificStockForm);
});

addmultiplestockBtn.addEventListener('click', () => {
    showsweetalert(addmultiplestockform);
});

editstockBtn.addEventListener('click', () => {
    showsweetalert(editStockForm);
});

editproductBtn.addEventListener('click', () => {
    showsweetalert(editProductForm);
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
