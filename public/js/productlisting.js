// View Product Listing
function viewproductlisting() {
    var viewproductlisting = document.getElementById("viewproductlisting");
    viewproductlisting.style.display = "block";
}
function closeproductlisting() {
    var viewproductlisting = document.getElementById("viewproductlisting");
    viewproductlisting.style.display = "none";
}
// Viewe Product Listing

// Modal for Add Product Listing
function addproductlisting() {
    var addproductlisting = document.getElementById("addproductlisting");
    addproductlisting.style.display = "block";
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
function editproductlisting() {
    var editproductlisting = document.getElementById("editproductlisting");
    editproductlisting.style.display = "block";
}
function closeeditproductlisting() {
    var editproductlisting = document.getElementById("editproductlisting");
    editproductlisting.style.display = "none";
}
