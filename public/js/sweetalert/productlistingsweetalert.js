const addproductlistingBtn = document.getElementById('addproductlistingBtn');
    const addproductlistngform = document.getElementById('addproductlistingform');

    const editproductlistingBtn = document.getElementById('editproductlistingBtn');
    const editproductlistingform = document.getElementById('editproductlistingform');

    addproductlistingBtn.addEventListener('click', () => {
        sweetalert(addproductlistngform);
    });

    editproductlistingBtn.addEventListener('click', () => {
        sweetalert(editproductlistingform);
    });
    

    function sweetalert(form) {
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
        });
    }