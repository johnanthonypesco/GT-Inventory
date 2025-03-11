const checkoutBtn = document.getElementById("checkoutbtn");
const ordersummary = document.getElementById("ordersummaryform");


checkoutBtn.addEventListener("click", (event) => {
    event.preventDefault(); 

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, checkout it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title:'Checkout!',
                text: 'Your order has been placed.',
                icon: 'success'
            }).then(() => {
                ordersummary.submit();
            });
        } else {
            Swal.fire({
                title: 'Cancelled',
                text: 'Your order is safe.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        }
    });
});