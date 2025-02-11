var viewOrder = document.getElementById("view-order");
var orderModal = document.getElementById("order-modal");
var modalClose = document.getElementById("modal-close");

viewOrder.addEventListener("click", () => {
    orderModal.style.display = "block";
});

modalClose.addEventListener("click", () => {
    orderModal.style.display = "none";
});

window.addEventListener("click", (e) => {
    if (e.target == orderModal) {
        orderModal.style.display = "none";
    }
});

var addNewOrder = document.getElementById("add-new-order");
var addNewOrderModal = document.querySelector(".add-new-order-modal");

addNewOrder.onclick = function () {
    addNewOrderModal.style.display = "block";
};

var close = document.querySelector(".close");

close.onclick = function () {
    addNewOrderModal.style.display = "none";
};

window.onclick = function (e) {
    if (e.target == addNewOrderModal) {
        addNewOrderModal.style.display = "none";
    }
};

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
