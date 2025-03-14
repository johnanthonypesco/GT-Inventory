function editaccount(){
    var editaccount = document.getElementById('editaccount');
    editaccount.classList.remove('hidden');
}

function closeeditaccount(){
    var editaccount = document.getElementById('editaccount');
    editaccount.classList.add('hidden');
}

window.addEventListener('resize', () => {
    var editaccount = document.getElementById('editaccount');
    editaccount.classList.add('hidden');
})
window.onclick = function(event) {
    var editaccount = document.getElementById('editaccount');
    if (event.target == editaccount) {
        editaccount.classList.add('hidden');
    }
}
function showpassword(){
    let password = document.getElementById('accountpassword');
    let eye = document.getElementById('eye');
    if(password.type == 'password'){
        password.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    }else{
        password.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}
function editshowpassword(){
    let password = document.getElementById('editpassword');
    let eye = document.getElementById('eye2');
    if(password.type == 'password'){
        password.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    }else{
        password.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}
function modalreenterpassword(){
    var modalreenterpassword = document.getElementById('modalreenterpassword');
    if(modalreenterpassword.type == 'password'){
        modalreenterpassword.type = 'text';
    }else{
        modalreenterpassword.type = 'password';
    }
}