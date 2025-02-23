<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{asset ('css/productlisting.css')}}">
    <title>Product Listing</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full">
        <x-admin.header title="Product Deals" icon="fa-solid fa-list-check" name="John Anthony Pesco" gmail="admin@gmail"/>

        <div class="w-full mt-5 bg-white p-5 rounded-lg">
            {{-- Customer List Search Function --}}
            <div class="flex flex-col md:flex-row justify-between items-center">
                <h1 class="font-bold text-2xl text-[#005382]">Customer List</h1>
                <x-input name="search" placeholder="Search Customer by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>        
            </div>
            {{-- Customer List Search Function --}}

            {{-- Table for customer List --}}
            <div class="table-container mt-5 overflow-auto h-[380px]">
                <x-table :headings="['Customer ID', 'Customer Name', 'Total Personalized Products', 'Action']" category="productdeals"/>
            </div>
            {{-- Table for customer List --}}

            {{-- pagination --}}
            <x-pagination/>
            {{-- pagination --}}
        </div>
    </main>

    {{-- View Product Listing --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="viewproductlisting">
        <div class="modal w-full md:w-[80%] h-fit md:h-full m-auto rounded-lg bg-white p-10 relative">
            <x-modalclose click="closeproductlisting"/>
            <div class="flex flex-col md:flex-row md:justify-between items-center">
                <h1 class="text-3xl font-semibold text-[#005382]">Jewel Velasquez</h1>
                {{-- Button for Search --}}
                <div class="w-full md:w-[35%] relative">
                    <input type="search" placeholder="Search Product Name" class="w-full p-2 rounded-lg outline-none border border-[#005382]">
                    <button class="border-l-1 border-[#005382] px-3 cursor-pointer text-xl absolute right-2 top-2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div> 
                {{-- Button for Search --}}           
            </div>
            {{-- Table for all products --}}
            <div class="table-container mt-5 overflow-auto h-[50vh] lg:h-[80%]">
                <table>
                    <thead>
                        <tr>
                            <th>Brand Name</th>
                            <th>Generic Name</th>
                            <th>Form</th>
                            <th>Strength</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Arcemit</td>
                            <td>Metoclopramide</td>
                            <td>Vials</td>
                            <td>10mg/ 10ml</td>
                            <td>₱ 1,000</td>
                            <td>
                                <div class="flex gap-3 items-center justify-center text-xl">
                                    <x-editbutton onclick="editproductlisting()"/>
                                    <x-deletebutton route="admin.productlisting" method="DELETE"/>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Table for all products --}}
            {{-- Pagination --}}
            <x-pagination/>
            {{-- Pagination --}}
        </div>
    </div>
    {{-- VIew Product Listing --}}

    {{-- Modal for Add Product Listing --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="addproductlisting">
        <div class="modal w-full md:w-[40%] h-full m-auto rounded-lg bg-white p-10 relative">
            <x-modalclose click="closeaddproductlisting"/>
            {{-- Form --}}
            <form action="" class="h-[75%]" id="addproductlistingform">
                <h1 class="text-center font-bold text-3xl text-[#005382]">List New Product</h1>

                <div class="h-full overflow-auto">
                    <div>
                        <x-label-input label="Customer Name" name="customername" type="text" for="customername" divclass="mt-5" placeholder="Enter Customer Name" value="Jewel Velasquez" disabled/>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2 relative" id="addmoreproductlist">
                        <div>
                            <x-label-input label="Product Name" name="product" type="text" for="product" placeholder="Enter Product Name"/>
                        </div>
                        <div>
                            <x-label-input label="Product Price" name="productprice" type="text" for="productprice" placeholder="Enter Product Price"/>
                        </div>
                    </div>

                </div>

                <div class="flex justify-between absolute bottom-0 w-full left-0 pb-5 h-fit px-10">
                    <button class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer" onclick="addmoreproductlisting()"><i class="fa-solid fa-plus"></i>Add More</button>
                    <x-submit-button id="addproductlistingBtn"/>
                </div>
            </form>
             {{--Form  --}}
        </div>
    </div>
    {{-- Modal for Add Product Listing --}}

    {{-- Edit Product Listing --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="editproductlisting">
        <div class="modal w-full md:w-[40%] h-fit m-auto rounded-lg bg-white p-10 relative">
            <x-modalclose click="closeeditproductlisting"/>
            {{-- Form --}}
            <form action="" id="editproductlistingform">
                <h1 class="text-center font-bold text-3xl text-[#005382]">Edit Product</h1>

                <x-label-input label="Product Name" name="price" type="text" for="brandname" divclass="mt-5" placeholder="Enter Account Name"/>
                <x-label-input label="Product Price" name="price" type="text" for="genericname" divclass="mt-5" placeholder="Enter Username"/>
                <x-submit-button id="editproductlistingBtn"/>
            </form> 
            {{-- Form --}}
        </div>
    </div>
</body>

<script src="{{asset('js/productlisting.js')}}"></script>
<script src="{{asset ('js/sweetalert/productlistingsweetalert.js')}}"></script>
</html>