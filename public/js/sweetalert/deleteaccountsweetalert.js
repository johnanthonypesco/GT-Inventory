
const deleteaccountform = document.getElementById('deleteaccountform');
const deleteaccount = document.getElementById('deleteaccountbtn');

deleteaccount.addEventListener('click', () => {
   Swal.fire ({
       title: 'Are you sure?',
       text: "You won't be able to revert this!",
       icon: 'question',
       showCancelButton: true,
       confirmButtonColor: '#3085d6',
       cancelButtonColor: '#d33',
       confirmButtonText: 'Yes, delete it!'
   }).then((result) => {
       if (result.isConfirmed) {
           Swal.fire({
               title: 'Deleted!',
               text: 'Your account has been successfully deleted.',
               icon: 'success',
               confirmButtonColor: '#3085d6'
           }).then(() => {
               deleteaccountform.submit();
           });
       } else {
           Swal.fire({
               title: 'Cancelled',
               text: 'Your account is safe.',
               icon: 'error',
               confirmButtonColor: '#3085d6'
           });
       }
   })
})