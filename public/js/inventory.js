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

function addstock(product_id, product_name) {
    const name = document.getElementById('single_add_name');
    const product_id_input = document.getElementById('single_product_id');
    
    name.innerHTML = product_name;
    product_id_input.value = product_id;

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

// MULTIPLE STOCK ADD SECTIONS
function add_more_stocks_input(current_clones) {
        // Get the html inside the template tag
        const template = document.getElementById('stock-entry-template').content.cloneNode(true);
    
        // Clears all values in inputs
        template.querySelectorAll('input').forEach(input => input.value = '');
    
        // Inserts the cloned template to the container
        document.getElementById('template-container').appendChild(template);
}
// MULTIPLE STOCK ADD SECTIONS

// SEARCH FUNCTION SECTION
// Prevents user from submitting form with Enter key
document.querySelectorAll('input[type=search]').forEach((search) => {
    search.addEventListener('keydown', (event)  => {
        if(event.key === "Enter") {
            event.preventDefault();
            is_in_suggestion()
        }
    })
});

//The function checks if the input is in the data list
function is_in_suggestion(id, list_id) {
    const search = document.getElementById(id);
    const search_options = document.getElementById(list_id).options;
    let in_suggestions = false;

    for (let i = 0 ; i < search_options.length ; i++) {
        if(search.value === search_options[i].value) {
            in_suggestions = true;
            break
        }
    }

    if (!in_suggestions) {
        search.classList.remove('border-[#005382]');
        search.classList.add('border-rose-500');
        alert("Choose from Search Suggestions");
    } else {
        document.getElementById('search-form-' + id).submit();
    }
}
// SEARCH FUNCTION SECTION