const reorderButton = document.getElementById('reorderBtn'); 
const reorderForm = document.getElementById('reorderForm'); 

if(reorderButton) { 
    reorderButton.addEventListener('click', function () { 
        Swal.fire({ 
            title: 'Are you sure?', 
            text: "This will create a new pending order with the items from your last purchase.", 
            icon: 'question', 
            showCancelButton: true, 
            confirmButtonColor: '#005382', 
            cancelButtonColor: '#d33', 
            confirmButtonText: 'Yes, re-order it!' 
        }).then((result) => { 
            if (result.isConfirmed) { 
                Swal.fire({
                    title: 'Re-ordering...', 
                    text: 'Please wait while we process your request.', 
                    allowOutsideClick: false, 
                    didOpen: () => { 
                        Swal.showLoading(); 
                    } 
                }); 
                reorderForm.submit(); 
            } 
        });
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