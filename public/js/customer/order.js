let purchaseFormState = {};
let deal_ids;

// will be used to insert all of the formatted data
const orderSummaryContent = document.getElementById("order-summary-content");

//will be used for user_id input cloning
const userIDInputValue = document.getElementById("user_id").dataset.value;

function updatePurchaseOrder(deal_id, quantity, product_name, price) {    
    if (deal_id in purchaseFormState) { // updates the item quantity if it does exist in the object
        purchaseFormState[deal_id][0] += Number(quantity);

        updateSubtotalPrice();

    } else { // Initializes the item if the deal_id doesn't exist yet in the object
        purchaseFormState[deal_id] = [Number(quantity), product_name, price];

        updateSubtotalPrice();
    }

    deal_ids = Object.keys(purchaseFormState);

    orderSummaryContent.innerHTML = "";
    
    Object.entries(purchaseFormState).forEach(([deal_id, orderData]) => {
        // orderData[0] = quantity, orderData[1] = product_name, orderData[2] = price
        orderSummaryContent.innerHTML += `
            <div id="item-parent-${deal_id}" class="order-item mt-2">

                <input type="hidden" id="user_id" required type="number" value="${userIDInputValue}" name="user_id[]">

                <input type="hidden" required type="number" value="${deal_id}" name="exclusive_deal_id[]">

                <div class="flex items-center justify-between gap-2 mb-2">
                    <div class="flex gap-2 items-center">
                        <i id="del-btn-${deal_id}" 
                        onclick="deleteOrdereredItem('del-btn-${deal_id}', 'item-parent-${deal_id}', ${deal_id})" class="fa-solid fa-trash -mt-[1px] text-red-600 p-3 rounded-xl border-2 border-red-500 hover:cursor-pointer hover:text-white hover:bg-red-500 transition-all duration-100"></i>
                        <p class="pname font-bold uppercase text-[#005382]">${orderData[1]}</p>
                    </div>

                    <input name="quantity[]" type="number" class="quantity w-[50px] px-2 border border-[#005382] rounded-xl" 
                    value="${orderData[0]}" min="1">
                </div>
                <div class="flex gap-2 items-center">
                    <p>Base Price:</p>
                    <p class="quantiyz font-bold">
                        P ${(orderData[2]).toLocaleString("en-US", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}
                    </p>
                </div>
                <div class="flex gap-2 items-center">
                    <p>Calculated Price:</p>
                    <p class="quantiyz font-bold">
                        P ${(orderData[0] * orderData[2]).toLocaleString("en-US", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}
                    </p>
                </div>
                <hr class="h-1 bg-[#005382] rounded-md border-none my-2">
            </div>`;
    });
}

function deleteOrdereredItem(deleteBtnID, parentID, deal_id) {
    const parent = document.getElementById(parentID);

    if (parent) {
        parent.remove()
        delete purchaseFormState[deal_id]

        updateSubtotalPrice()
    }
}

function updateSubtotalPrice() {
    const display = document.getElementById('subtotal');

    let total = 0;
    
    Object.values(purchaseFormState).map(values => {
        total += values[0] * values[2];
    });

    display.innerText = "₱ " + total.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}
function viewOrderSummary() {
    document.getElementById("order-summary-content").classList.toggle("h-[45vh]");
    document.getElementById("order-summary-content").classList.toggle("-h-[10px]");
    document.getElementById("order-summary-content").classList.toggle("max-h-[20vh]");
    document.getElementById("order-summary-content").classList.toggle("-max-h-[10px]");
    document.getElementById("ordersummaryicon").classList.toggle("rotate-180");
}


const checkoutBtn = document.getElementById("checkoutbtn");
const ordersummary = document.getElementById("ordersummaryform");

checkoutBtn.addEventListener("click", (event) => {
    event.preventDefault(); 

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, checkout it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title:'Checkout!',
                text: 'Your order has been placed.',
                icon: 'success'
            }).then(() => {
                ordersummary.submit();
            });
        } else {
            Swal.fire({
                title: 'Cancelled',
                text: 'Your order is safe.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        }
    });
});