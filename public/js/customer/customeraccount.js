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
function password(){
    var password = document.getElementById('password');
    if(password.type == 'password'){
        password.type = 'text';
    }else{
        password.type = 'password';
    }
}
function modalpassword(){
    var modalpassword = document.getElementById('modalpassword');
    if(modalpassword.type == 'password'){
        modalpassword.type = 'text';
    }else{
        modalpassword.type = 'password';
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