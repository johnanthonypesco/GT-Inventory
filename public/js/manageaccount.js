// Add Account Modal
function addaccount() {
    var addaccount = document.getElementById("addaccount");
    addaccount.style.display = "block";
}

function closeaddaccount() {
    var addaccount = document.getElementById("addaccount");
    addaccount.style.display = "none";
}
// End of Add Account Modal


// Edit Account Modal
function editaccount() {
    var editaccount = document.getElementById("editAccountModal");
    editaccount.classList.replace("hidden","block")
}

function closeEditAccountModal() {
    var editaccount = document.getElementById("editAccountModal");
    editaccount.classList.replace("block","hidden")
}
// End of Edit Account Modal