function companyChosen() {
    const company = document.getElementById('company-select');
    const hiddenInputsDiv = document.getElementById('create-order-hidden-inputs');

    hiddenInputsDiv.className = 'flex flex-col gap-3';

    const userInput = document.querySelector('[name="user_id"]');
    const dealsInput = document.querySelector('[name="exclusive_deal_id"]');

    userInput.setAttribute('list', `create-suggestions-${company.value}`);
    dealsInput.setAttribute('list', `available-deals-${company.value}`);
}