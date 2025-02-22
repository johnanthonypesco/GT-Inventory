var modal = document.getElementById("addmodal");
var btn = document.getElementById("openModal");
var span = document.getElementsByClassName("close")[0];
var addmore = document.getElementById("addmore");
var addform = document.getElementById("addform");

btn.onclick = function () {
    modal.style.display = "block";
};

span.onclick = function () {
    modal.style.display = "none";
};

window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
};

// View All Products
function viewallproduct() {
    var viewallproductmodal = document.getElementById("viewallproductmodal");
    viewallproductmodal.style.display = "block";
}
function closeviewallproduct() {
    var viewallproductmodal = document.getElementById("viewallproductmodal");
    viewallproductmodal.style.display = "none";
}
// View All Products

// Register New Product
function registerproduct() {
    var registerproductmodal = document.getElementById("registerproductmodal");
    registerproductmodal.style.display = "block";
}
function closeregisterproductmodal() {
    var registerproductmodal = document.getElementById("registerproductmodal");
    registerproductmodal.style.display = "none";
}
// Register New Product


// SHOW THE STOCK PRODUCTS POPUP MODAL
function showStockModals(type) {
    const modalInStock = document.getElementById("in-stock-modal");
    const modalLowStock = document.getElementById("low-stock-modal");
    const modalOutStock = document.getElementById("out-stock-modal");

    if (type === "in-stock") {
        if(modalInStock.classList.contains("hidden")){
            modalInStock.classList.replace("hidden","block");
        } else {
            modalInStock.classList.replace("block", "hidden");
        }
    } else if (type === "low-stock") {
        if(modalLowStock.classList.contains("hidden")) {
            modalLowStock.classList.replace("hidden","block");
        } else {
            modalLowStock.classList.replace("block", "hidden");      
        }
    } else if (type === "out-stock") {
        if(modalOutStock.classList.contains("hidden")) {
            modalOutStock.classList.replace("hidden","block");
        } else {
            modalOutStock.classList.replace("block", "hidden");      
        }
    }
}

function addstock() {
    var addstock = document.getElementById("addstock");
    addstock.style.display = "block";
}
function closeaddstock() {
    var addstock = document.getElementById("addstock");
    addstock.style.display = "none";
}

function addmultiplestock() {
    var addmultiplestock = document.getElementById("addmultiplestock");
    addmultiplestock.style.display = "block";
}
function closeaddmultiplestock() {
    var addmultiplestock = document.getElementById("addmultiplestock");
    addmultiplestock.style.display = "none";
}

// SHOW THE STOCK PRODUCTS POPUP MODAL