// View Product Listing
function viewproductlisting(username) {
    var viewproductlisting = document.getElementById("view-listings-" + username);
    viewproductlisting.style.display = "block";
}
function closeproductlisting(username) {
    var viewproductlisting = document.getElementById("view-listings-" + username);
    viewproductlisting.style.display = "none";
}
// Viewe Product Listing

// Modal for Add Product Listing
function addproductlisting(customer_id) {
    const addproductlisting = document.getElementById("addproductlisting");
    const customer_id_input = document.getElementById("company-id");

    addproductlisting.style.display = "block";
    customer_id_input.value = customer_id;
}
function addmoreproductlisting() {
    event.preventDefault();
    var addmoreproductlisting = document.getElementById("addmoreproductlist");
    var clone = addmoreproductlisting.cloneNode(true);
    addmoreproductlisting.parentNode.appendChild(clone);
}
function closeaddproductlisting() {
    var addmoreproductlisting = document.getElementById("addproductlisting");
    addmoreproductlisting.style.display = "none";
}
// Modal for Add Product Listing

// Modal for Edit Product Listing
function editProductListing(id) {
    const editproductlisting = document.getElementById("edit-listing-" + id);
    
    editproductlisting.classList.replace("-mt-[1000px]", "block");
}
function closeEditProductListing(id) {
    const editproductlisting = document.getElementById("edit-listing-" + id);
    editproductlisting.classList.replace("block", "-mt-[1000px]");
}
