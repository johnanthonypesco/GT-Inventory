const checkoutBtn = document.getElementById("checkoutbtn");
const ordersummary = document.getElementById("ordersummaryform");


checkoutBtn.addEventListener("click", (event) => {
    event.preventDefault(); 

    Swal.fire({
        title: 'Are you sure?',
        text: "You're about to checkout your order!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, checkout it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title:'Processing...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            ordersummary.submit();
        }
    });    
});

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