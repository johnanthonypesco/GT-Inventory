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

// EDIT REGISTERED PRODUCTS
function editRegisteredProduct(prod_id, prod_generic, prod_brand, prod_form, prod_strength, dir_Imgprefix, prod_img) {
    const modal = document.getElementById('edit-registered');
    const actual_form = document.getElementById('edit-prod-reset');
    const h1 = document.getElementById('title-prod-edit');
    const displayImg = document.getElementById("prod-img"); // the current IMG for the modal

    const id = document.getElementById("edit-prod-id");
    const generic = document.getElementById("edit-prod-generic");
    const brand = document.getElementById("edit-prod-brand");
    const form = document.getElementById("edit-prod-form");
    const strength = document.getElementById("edit-prod-strength");
    const img = document.getElementById("edit-prod-img"); // the input

    if (modal.classList.contains('-mt-[0px]')) {
        modal.classList.replace('-mt-[0px]', '-mt-[4000px]');

        console.log("CLOSING UPDATE MODAL")
        actual_form.reset();
        displayImg.src = dir_Imgprefix;
        h1.innerHTML = "UPDATING: NOTHING";
        
        return;
    } 

    // console.log("SHOWING UPDATE MODAL");
    modal.classList.replace('-mt-[4000px]', '-mt-[0px]');
    h1.innerHTML = `Updating Product ID: ${prod_id}`;
    id.value = prod_id;
    generic.value = prod_generic;
    brand.value = prod_brand;
    form.value = prod_form;
    strength.value = prod_strength;
    displayImg.src = dir_Imgprefix + prod_img;
}

// EDIT REGISTERED PRODUCTS


// SHOW THE STOCK PRODUCTS POPUP MODAL
function showStockModals(type) {
    const modalInStock = document.getElementById("in-stock-modal");
    const modalLowStock = document.getElementById("low-stock-modal");
    const modalOutStock = document.getElementById("out-stock-modal");
    const modalNearExpiredStock = document.getElementById("near-expiry-stock-modal");
    const modalExpiredStock = document.getElementById("expired-stock-modal");

    switch (type) {
        case "in-stock":
        if(modalInStock.classList.contains("hidden")){
            modalInStock.classList.replace("hidden","block");
        } else {
            modalInStock.classList.replace("block", "hidden");
        }
            break;
        case "low-stock":
        if(modalLowStock.classList.contains("hidden")) {
            modalLowStock.classList.replace("hidden","block");
        } else {
            modalLowStock.classList.replace("block", "hidden");      
        }
            break;
        case "out-stock": 
        if(modalOutStock.classList.contains("hidden")) {
            modalOutStock.classList.replace("hidden","block");
        } else {
            modalOutStock.classList.replace("block", "hidden");      
        }
            break;
        case "near-expiry-stock":
            if(modalNearExpiredStock.classList.contains("hidden")){
                modalNearExpiredStock.classList.replace("hidden","block");
            } else {
                modalNearExpiredStock.classList.replace("block", "hidden");
            }
            break;
        case "expired-stock":
            if(modalExpiredStock.classList.contains("hidden")){
                modalExpiredStock.classList.replace("hidden","block");
            } else {
                modalExpiredStock.classList.replace("block", "hidden");
            }
            break;
        default:
            console.error("No IDS found for that stock modal")
            break;
    }

    // if (type === "in-stock") {
    //     if(modalInStock.classList.contains("hidden")){
    //         modalInStock.classList.replace("hidden","block");
    //     } else {
    //         modalInStock.classList.replace("block", "hidden");
    //     }
    // } else if (type === "low-stock") {
    //     if(modalLowStock.classList.contains("hidden")) {
    //         modalLowStock.classList.replace("hidden","block");
    //     } else {
    //         modalLowStock.classList.replace("block", "hidden");      
    //     }
    // } else if (type === "out-stock") {
    //     if(modalOutStock.classList.contains("hidden")) {
    //         modalOutStock.classList.replace("hidden","block");
    //     } else {
    //         modalOutStock.classList.replace("block", "hidden");      
    //     }
    // }
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

    document.getElementById('single_product_id').value = "";
    document.getElementById('single_batch_number').value = "";
    document.getElementById('single_quantity').value = "";
    document.getElementById('single_expiry').value = "";

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
        event.preventDefault();
        alert("Choose from Search Suggestions");
    } else {
        document.getElementById('search-form-' + id).submit();
    }
}

function openTransferModal(inventoryId, batchNumber, productName, currentLocation) {
    document.getElementById('transfer_inventory_id').value = inventoryId;
    document.getElementById('transfer_batch_number').textContent = batchNumber;
    document.getElementById('transfer_product_name').textContent = productName;
    document.getElementById('transfer_current_location').textContent = currentLocation;
    
    document.getElementById('transferInventoryModal').classList.remove('hidden');
}

function closeTransferModal() {
    document.getElementById('transferInventoryModal').classList.add('hidden');
}

// Handle Transfer Form Submission
document.getElementById('transferForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = {
        inventory_id: document.getElementById('transfer_inventory_id').value,
        new_location: document.getElementById('new_location').value
    };

    fetch("{{ route('admin.inventory.transfer') }}", {
        method: "POST",
        body: JSON.stringify(formData),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            Swal.fire("Success", data.message, "success").then(() => {
                location.reload();
            });
        } else {
            Swal.fire("Error", data.message, "error");
        }
    })
    .catch(() => Swal.fire("Error", "Failed to connect to the server.", "error"));
});

// SEARCH FUNCTION SECTION