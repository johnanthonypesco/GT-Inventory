function viewOrder() {
    var viewOrderModal = document.querySelector(".order-modal");
    viewOrderModal.style.display = "block";
}
function closeOrderModal() {
    var viewOrderModal = document.querySelector(".order-modal");
    viewOrderModal.style.display = "none";
}

window.addEventListener("click", function (e) {
    var viewOrderModal = document.querySelector(".order-modal");
    if (e.target == viewOrderModal) {
        viewOrderModal.style.display = "none";
    }
});

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
