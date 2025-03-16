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

function editshowconfirmpassword(){
    let password = document.getElementById('editconfirmpassword');
    let eye = document.getElementById('eye3');
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

// password mismatch
document.addEventListener('DOMContentLoaded', function () {
    const passwordField = document.getElementById('editpassword');
    const confirmPasswordField = document.getElementById('editconfirmpassword');
    const passwordMismatch = document.getElementById('passwordmismatch');

    function checkPasswordMatch() {
        if (confirmPasswordField.value === "") {
            passwordMismatch.classList.add('hidden'); 
        } else if (confirmPasswordField.value !== passwordField.value) {
            passwordMismatch.classList.remove('hidden');
            passwordMismatch.textContent = "Passwords do not match";
            passwordMismatch.classList.remove('text-green-500');
            passwordMismatch.classList.add('text-red-500');
        } else {
            passwordMismatch.classList.remove('hidden');
            passwordMismatch.textContent = "Passwords match!";
            passwordMismatch.classList.remove('text-red-500');
            passwordMismatch.classList.add('text-green-500');
        }
    }

    passwordField.addEventListener('input', checkPasswordMatch);
    confirmPasswordField.addEventListener('input', checkPasswordMatch);
});

// Open the edit account modal
function editAccount() {
    let modal = document.getElementById("editAccountModal");
    if (modal) {
        modal.classList.remove("hidden");
    } else {
        console.error("Error: Modal with ID 'editAccountModal' not found.");
    }
}

// Close the edit account modal
function closeEditAccount() {
    let modal = document.getElementById("editAccountModal");
    if (modal) {
        modal.classList.add("hidden");
    } else {
        console.error("Error: Modal with ID 'editAccountModal' not found.");
    }
}
