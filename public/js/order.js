function viewOrder(id) {
    var viewOrderModal = document.getElementById("order-modal-" + id);
    viewOrderModal.classList.replace("hidden", "flex");
}
function closeOrderModal(id) {
    var viewOrderModal = document.getElementById("order-modal-" + id);
    viewOrderModal.classList.replace("flex", "hidden");
}

function addneworder() {
    var addOrderModal = document.querySelector(".add-new-order-modal");
    addOrderModal.style.display = "block";
}
function closeaddneworder() {
    var addOrderModal = document.querySelector(".add-new-order-modal");
    addOrderModal.style.display = "none";
}

function uploadqr() {
    var uploadQrModal = document.querySelector(".upload-qr-modal");
    uploadQrModal.style.display = "block";
}

function closeuploadqrmodal() {
    var uploadQrModal = document.querySelector(".upload-qr-modal");
    uploadQrModal.style.display = "none";
}

function showInsufficients() {
    const summaryDiv = document.getElementById("insufficientsModal");

    if(summaryDiv.classList.contains("hidden")) {
        summaryDiv.classList.replace("hidden", "flex");
    } else {
        summaryDiv.classList.replace("flex", "hidden");
    }
}

function showChangeStatusModal(id, motherDiv, archivingDetails) {    
    const summaryDiv = document.getElementById("change-status-modal");
    const motherInput = document.getElementById("mother-id"); 
    const statusInputId = document.getElementById("id-container"); // the damn order ID

    const province = document.getElementById("archive-province");
    const company = document.getElementById("archive-company");
    const employee = document.getElementById("archive-employee");
    const date = document.getElementById("archive-date-ordered");
    const generic = document.getElementById("archive-generic-name");
    const brand = document.getElementById("archive-brand-name");
    const form = document.getElementById("archive-form");
    const quantity = document.getElementById("archive-quantity");
    const price = document.getElementById("archive-price");
    const subtotal = document.getElementById("archive-subtotal");

    if(summaryDiv.classList.contains("hidden")) {
        summaryDiv.classList.replace("hidden", "flex");

        statusInputId.dataset.id = id;
        statusInputId.value = id;
        motherInput.value = motherDiv;

        province.value = archivingDetails.province;
        company.value = archivingDetails.company;
        employee.value = archivingDetails.employee;       // ✅ FIXED
        date.value = archivingDetails.date_ordered;
        generic.value = archivingDetails.generic_name; // ✅ FIXED
        brand.value = archivingDetails.brand_name;     // ✅ FIXED
        form.value = archivingDetails.form;
        quantity.value = archivingDetails.quantity;
        price.value = archivingDetails.price;
        subtotal.value = archivingDetails.subtotal;
    } else {
        summaryDiv.classList.replace("flex", "hidden");
}
        statusInputId.dataset.id = 0;
        motherInput.value = 0;

        province.value = '';
        company.value = '';
        employee.value = '';
        date.value = '';
        generic.value = '';
        brand.value = '';
        form.value = '';
        quantity.value = 0;
        price.value = 0;
        subtotal.value = 0;
    }


function changeStatus(form, statusType) {
    const idContainer = document.getElementById("id-container");
    const statusID = document.getElementById("status-id");

    statusID.value = statusType.toLowerCase();

    // ayoko gumamit ng pattern finder algoritmn so i-deconstruct ko nalang string :)
    const separated = form.action.split("admin/orders")
    
    // yung separed[0] is yung http://127.0.0.1:8000/
    form.action = separated[0] + "admin/orders/" +  idContainer.dataset.id;

    confirm("Change Status??? are you sure???") ? form.submit() : null
}
