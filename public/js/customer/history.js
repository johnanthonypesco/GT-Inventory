function viewOrder(id) {
    var viewOrderModal = document.getElementById("view-order-modal-" + id);
    viewOrderModal.style.display = "block";
}
function closeOrderModal(id) {
    var viewOrderModal = document.getElementById("view-order-modal-" + id);
    viewOrderModal.style.display = "none";
}

// window.addEventListener("click", function (e) {
//     var viewOrderModal = document.querySelector(".order-modal");
//     if (e.target == viewOrderModal) {
//         viewOrderModal.style.display = "none";
//     }
// });

function addneworder() {
    var addOrderModal = document.querySelector(".add-new-order-modal");
    addOrderModal.style.display = "block";
}
function closeaddneworder() {
    var addOrderModal = document.querySelector(".add-new-order-modal");
    addOrderModal.style.display = "none";
}

window.addEventListener("click", function (e) {
    var addOrderModal = document.querySelector(".add-new-order-modal");
    if (e.target == addOrderModal) {
        addOrderModal.style.display = "none";
    }
});

document
    .getElementById("addnewworder-button")
    .addEventListener("click", function (event) {
        event.preventDefault();

        var addform = document.getElementById("order-form-input");

        var separator = document.createElement("hr");
        separator.classList.add("my-4", "border-t", "border-black");

        var clone = addform.cloneNode(true);

        clone.querySelectorAll("input").forEach((input) => {
            input.value = "";
        });

        addform.parentNode.appendChild(separator);
        addform.parentNode.appendChild(clone);
    });


// SIGRAE EMPLOYEE SEARCH SUGGESTION CODES
function isInSuggestionDeal () {
    const input = document.getElementById('deal_search');
    const dataList = document.getElementById('deal-search-suggestions');
    const options = Array.from(dataList.options).map(option => option.value);

    if (!options.includes(input.value)) {
        alert("Please Choose a Product Deal From The Search Suggestions.");
        
        return false;
    } else {
        return true;
    }
}
// SIGRAE EMPLOYEE SEARCH SUGGESTION CODES