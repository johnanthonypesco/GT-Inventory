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


addmore.onclick = function () {
    event.preventDefault();
    var list = document.createElement("div");
    list.classList.add("mt-5", "grid", "grid-cols-2", "gap-2");

    list.innerHTML = `
            <div>
                <label for="batch" class="text-[15px] font-semibold">Batch no.</label>
                <input type="text" name="batch[]" class="border p-1 w-full rounded-lg mt-1" placeholder="Enter Batch No.">
            </div>
            <div>
                <label for="brand" class="text-[15px] font-semibold">Brand Name:</label>
                <input type="text" name="brand[]" class="border p-1 w-full rounded-lg mt-1" placeholder="Enter Brand Name">
            </div>
            <div>
                <label for="generic" class="text-[15px] font-semibold">Generic Name:</label>
                <input type="text" name="generic[]" class="border p-1 w-full rounded-lg mt-1" placeholder="Enter Generic Name">
            </div>
            <div>
                <label for="form" class="text-[15px] font-semibold">Form:</label>
                <input type="text" name="form[]" class="border p-1 w-full rounded-lg mt-1" placeholder="Enter Form">
            </div>
            <div>
                <label for="quantity" class="text-[15px] font-semibold">Quantity:</label>
                <input type="text" name="quantity[]" class="border p-1 w-full rounded-lg mt-1" placeholder="Enter Quantity">
            </div>
            <div>
                <label for="expiry" class="text-[15px] font-semibold">Expiry Date</label>
                <input type="date" name="expiry[]" class="border p-1 w-full rounded-lg mt-1">
            </div>
            <hr class="border-t border-black w-[410px] mt-5">
        `;

    addform.appendChild(list);
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

