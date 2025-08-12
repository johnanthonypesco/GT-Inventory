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
    
    editproductlisting.classList.replace("-mt-[4000px]", "block");
}
function closeEditProductListing(id) {
    const editproductlisting = document.getElementById("edit-listing-" + id);
    editproductlisting.classList.replace("block", "-mt-[4000px]");
}

// checks to see if the search is in the suggestions
document.addEventListener('DOMContentLoaded', function () {
    const companyForm = document.getElementById('company-search-form');
    if (companyForm) {
        companyForm.addEventListener('submit', isInSuggestionCompany);
    }
});

function isInSuggestionCompany (e) {
    const input = document.getElementById('company-search');
    const dataList = document.getElementById('company-search-suggestions');
    const options = Array.from(dataList.options).map(option => option.value);

    if (!options.includes(input.value)) {
        e.preventDefault();
        alert("Please Choose a Company From The Search Suggestions.");
    }
}

function isInSuggestionDeal (form_id, input_id) {
    const form = document.getElementById(form_id);
    const input = document.getElementById(input_id);
    const dataList = document.getElementById('deal-search-suggestions');
    const options = Array.from(dataList.options).map(option => option.value);

    if (!options.includes(input.value)) {
        console.log(input.value);
        alert("Please Choose a Product Deal From The Search Suggestions.");
        return false;
    } else {
        return true;
    }
}

// SIGRAE ARCHIVE SECTION
function viewArchivedDeals() {
    const state = document.getElementById('view-archived-listings');

    if (state.classList.contains('hidden')) {
        state.classList.replace('hidden', 'block');

        return true;
    } else {
        state.classList.replace('block', 'hidden');
    }
}
// SIGRAE ARCHIVE SECTION