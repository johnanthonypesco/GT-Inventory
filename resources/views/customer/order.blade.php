<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <title>Order</title>
</head>
<body class="bg-[#BBBCBE] flex p-5 gap-5">
    <x-customer.navbar />

    <main class="w-full">
        <x-customer.header />

        <div class="flex flex-col lg:flex-row gap-5 mt-5">
            <!-- Available Products -->
            <div class="w-full lg:w-[70%] bg-white p-5 rounded-xl">
                <h1 class="font-semibold text-2xl">Available Products</h1>
                <div class="relative">
                    <input type="search" class="w-full mt-3 p-2 rounded-xl border border-[#005382] outline-none" placeholder="Search Order">
                    <button class="absolute right-1 top-5 border-l-1 border-[#005382] px-3 cursor-pointer text-xl">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
        
                <div class="h-[70vh] lg:h-[57vh] overflow-y-auto mt-5">
                    <!-- Product Form -->
                    <form class="product-form shadow-md shadow-[#005382]/70 flex flex-col lg:flex-row justify-between rounded-xl p-5">
                        <div class="flex gap-2">
                            <img src="{{ asset('image/download.jpg') }}" alt="" class="w-[100px] shadow-lg shadow-[#005382]/60 rounded-xl">
                            
                            <div class="flex flex-col gap-2 justify-center">
                                <p class="border border-[#005382] rounded-xl px-2 w-fit">Vials</p>
                                <h1 class="product-name">Arcemit</h1>
                                <p class="font-bold uppercase">Metoclopramide</p>
                                <div class="flex gap-2">
                                    <p class="flex items-center"><span class="text-[#005382] font-semibold">Form:</span> Vials</p>
                                    <p class="flex items-center"><span class="text-[#005382] font-semibold">Strength:</span> 10mg/10ml</p>
                                </div>
                            </div>
                        </div>
                        <div class="self-start mt-4 lg:mt-0 lg:self-end">
                            <p class="font-semibold flex gap-5">
                                <span class="text-[#005382]">Price:</span> 
                                <span class="product-price">$10000</span>
                            </p>
                            <!-- Quantity Input -->
                            <div class="flex gap-2 mt-2">
                                <input type="number" class="quantity w-[50px] p-2 border border-[#005382] rounded-xl" value="1" min="1">
                                <button type="submit" class="add-to-cart bg-[#005382] text-white p-2 rounded-xl">Add to Order</button>
                            </div>
                        </div>
                    </form>
                    <!-- End of Product Form -->
                </div>
            </div>
        
            <!-- Summary of Orders -->
            <div class="w-full lg:w-[30%] bg-white p-5 rounded-xl">
                <h1 class="text-center font-semibold text-2xl mb-5">Summary of Orders</h1>
                <div id="order-summary" class="h-[30vh] lg:h-[45vh] overflow-y-auto">
                    <!-- Orders will be appended here -->
                </div>
        
                <hr class="my-5">
        
                <div>
                    <h1 class="text-xl font-semibold text-right mt-5">Subtotal: <span id="subtotal">$0</span></h1>
                    <button class="bg-[#005382] text-white p-2 rounded-lg w-full mt-5">Checkout</button>
                </div>
            </div>
        </div>      
    </main>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const forms = document.querySelectorAll(".product-form");
        const orderSummary = document.getElementById("order-summary");
        const subtotalElement = document.getElementById("subtotal");
        let subtotal = 0;

        forms.forEach(form => {
            form.addEventListener("submit", function (e) {
                e.preventDefault();

                const productName = form.querySelector(".product-name").textContent;
                const price = parseInt(form.querySelector(".product-price").textContent.replace("$", ""));
                const quantity = parseInt(form.querySelector(".quantity").value);
                const total = price * quantity;

                const orderItem = document.createElement("div");
                orderItem.classList.add("flex", "justify-between", "mt-2");
                orderItem.innerHTML = `
                    <h1 class="text-sm font-semibold w-[60%]">${productName} x ${quantity}</h1>
                    <p class="text-sm font-semibold">Price: $${total}</p>
                `;

                orderSummary.appendChild(orderItem);

                subtotal += total;
                subtotalElement.textContent = `$${subtotal}`;
            });
        });
    });


    var currentLocation = window.location.href;
    var navLinks = document.querySelectorAll("nav a");
    navLinks.forEach(function (link) {
        if (link.href === currentLocation) {
            link.classList.add("active");
        }
    });
</script>
</html>
