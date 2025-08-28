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

                <input type="hidden" required type="number" value="${deal_id}" name="exclusive_deal_id[]">

                <div class="flex items-center justify-between gap-2 mb-2">
                    <div class="flex items-center gap-2 sm:gap-3 group">
                        <div id="del-btn-${deal_id}"
                            onclick="deleteOrdereredItem('del-btn-${deal_id}', 'item-parent-${deal_id}', ${deal_id})"
                            class="p-2 rounded-xl border-2 border-red-500 hover:cursor-pointer hover:bg-red-500 transition-all duration-100">
                            <i class="fa-regular fa-trash-can text-red-500 text-sm group-hover:text-white"></i>
                        </div>
                        <p class="pname font-bold uppercase text-[#005382] text-sm sm:text-base md:text-lg">${orderData[1]}</p>
                    </div>

                    <input name="quantity[]" readonly type="number" class="quantity w-[100px] px-2 border border-[#005382] rounded-xl" 
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

// SIGRAE EMPLOYEE SEARCH SUGGESTION CODES
function isInSuggestionDeal () {
    const input = document.getElementById('deal_search');
    const dataList = document.getElementById('deal-search-suggestions');
    const options = Array.from(dataList.options).map(option => option.value);

    if (!options.includes(input.value)) {
        alert("Please Choose a Product Deal From The Search Suggestions.");
        
        return false;
    } else {
        return true;
    }
}
// SIGRAE EMPLOYEE SEARCH SUGGESTION CODES

// SIGRAE SEARCH FILTER SHIT
// Panong gumagana to? You might ask... Hindi ko rin alam kaya ko rin tinatanong :)
// By: SigraeGPT

const searchInput = document.getElementById('deal_search');
const products = Array.from(document.querySelectorAll('.product-form'));

searchInput.addEventListener('input', () => {
  const filter = searchInput.value.toLowerCase().trim();

  products.forEach(productEl => {
    const generic = productEl.querySelector('.product-name')?.textContent.toLowerCase() || '';
    const brand   = productEl.querySelector('p.font-bold.uppercase')?.textContent.toLowerCase() || '';
    const form    = productEl.querySelector('.flex > p')?.textContent.toLowerCase() || '';
    const strength= productEl.querySelector('p.flex.items-center:nth-child(2)')?.textContent.toLowerCase() || '';
    const price   = productEl.querySelector('.product-price')?.textContent.toLowerCase() || '';

    const haystack = [generic, brand, form, strength, price].join(' - ');

    if (haystack.includes(filter)) {
        productEl.style.display = '';
    } else {
        productEl.style.display = 'none';
    }
    });
});
// SIGRAE SEARCH FILTER SHIT
