const addstockBtn = document.getElementById('addstockBtn');
const addmultiplestockBtn = document.getElementById('addmultiplestockBtn');
const addproductBtn = document.getElementById('addproductBtn');

const addproductform = document.getElementById('addproduct');
const addSpecificStockForm = document.getElementById('addspecificstock');
const addmultiplestockform = document.getElementById('addmultiplestockform');

addproductBtn.addEventListener('click', () => {
    showsweetalert(addproductform);
});

addstockBtn.addEventListener('click', () => {
    showsweetalert(addSpecificStockForm);
});

addmultiplestockBtn.addEventListener('click', () => {
    showsweetalert(addmultiplestockform);
});


function showsweetalert(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Saved!',
                text: 'Your Product has been successfully saved.',
                icon: 'success',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                form.submit();
            });
        } else {
            Swal.fire({
                title: 'Cancelled',
                text: 'Succesfully cancelled!',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        }
    })
}