<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{asset ('css/inventory.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Inventory</title>
</head>
<body class="flex flex-col md:flex-row gap-4 h-[100vh]">

    <x-admin.navbar/>

    <main class="md:w-full h-full">
        <x-admin.header title="Inventory" icon="fa-solid fa-boxes-stacked" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- Total Container --}}
        <div class="mt-3 flex gap-5 flex-wrap">
            <x-totalstock :count="$inStocks->count() ? $inStocks->count() : 0" title="Total In Stocks" image="image.png" buttontype="in-stock" />
            <x-totalstock :count="$lowStocks->count() ? $lowStocks->count() : 0" title="Total Low Stocks" image="image (1).png" buttontype="low-stock" />
            <x-totalstock :count="$outOfStocks->count() ? $outOfStocks->count() : 0" title="Total Out of Stocks" image="image (2).png" buttontype="out-stock" />
        </div>
        {{-- Total Container --}}

        {{-- Shows An Overview Modal for Certain Product Categories --}}
        <x-stock-overview-modal  modalType="in-stock" :variable="$inStocks" />
        <x-stock-overview-modal  modalType="low-stock" :variable="$lowStocks" />
        <x-stock-overview-modal  modalType="out-stock" :variable="$outOfStocks" />
        {{-- Shows An Overview Modal for Certain Product Categories --}}

        {{-- Filters Location --}}
        <div class="flex justify-between flex-col lg:flex-row">
            <select name="location" id="location" class="w-full md:w-fit border p-2 py-2 rounded-lg mt-10 sm:mt-2 h-10 text-center text-[#005382] font-bold bg-white outline-none">
                <option selected>All Location</option>  
                <option>Tarlac</option>
                <option>Cabanatuan</option>
            </select>

            <div class="flex gap-3 mt-2">
                <button class="bg-white text-sm font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer" onclick="viewallproduct()"><i class="fa-regular fa-eye"></i>View All Products</button>
                <button class="bg-white text-sm font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer" onclick="registerproduct()"><i class="fa-solid fa-plus"></i>Register New Product</button>
            </div>
        </div>
        {{-- Filters Location --}}

        <div class="table-container bg-white mt-2 p-3 px-6 rounded-lg">
            <div class="flex flex-wrap justify-between items-center">
                {{-- Search --}}
                <x-input name="searchproduct" placeholder="Search Product by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>
                {{-- Search --}}
                
                <div class="button flex items-center gap-3 mt-3 lg:mt-0 m-auto md:m-0">
                    <button id="openModal" class="flex items-center gap-1"><i class="fa-solid fa-plus"></i>Add Stocks</button>
                    <button class="flex items-center gap-1"><i class="fa-solid fa-list"></i>Filter</button>
                    <button class="flex items-center gap-1"><i class="fa-solid fa-download"></i>Export</button>
                </div>
            </div>

            {{-- Table for Inventory --}}
            <div class="overflow-auto h-[250px] mt-5">
                <x-table :headings="['Batch No.', 'Brand Name', 'Generic Name', 'Form', 'Stregth', 'Quantity', 'Expiry Date', 'Status']" :variable="$inventories" category="inventory"/>
            </div>
            {{-- Table for Inventory --}}

            {{-- Pagination --}}
            <x-pagination/>
            {{-- Pagination --}}
        </div>
    </main>

    {{-- Modal for View All Products --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="viewallproductmodal">
        <div class="modal w-full md:w-[80%] h-fit md:h-full m-auto rounded-lg bg-white p-10 relative">
            <span id="viewallproductclose" class="text-6xl font-bold text-red-600 cursor-pointer absolute -right-4 -top-8" onclick="closeviewallproduct()">&times;</span>
            <h1 class="font-bold text-2xl text-[#005382]">All Products</h1>

            <div class="flex justify-between flex-col lg:flex-row gap-5 mt-5">
                <button onclick="addmultiplestock()" class="bg-white w-fit font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer"><i class="fa-solid fa-plus"></i>Add Stocks</button>
                <x-input name="search" placeholder="Search Product by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>                 
            </div>

            {{-- Table for all products --}}
            <div class="table-container mt-5 overflow-auto h-[350px]">
                <table>
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Generic Name</th>
                            <th>Brand Name</th>
                            <th>Form</th>
                            <th>Strength</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="data">
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->id}}</td>
                                <td>{{ $product->generic_name ?? "none" }}</td>
                                <td>{{ $product->brand_name ?? "none" }}</td>
                                <td>{{ $product->form }}</td>
                                <td>{{ $product->strength }}</td>
                                <td class="flex items-center gap-4 justify-center">
                                    <button onclick="addstock()" class="cursor-pointer flex items-center gap-2 text-[#005382]"><i class="fa-solid fa-plus"></i>Add Stock</button>
                                    <x-delete-button route="admin.destroy.product" routeid="{{$product->id}}" method="DELETE" id="delete"/>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Table for all products --}}

            {{-- Pagination --}}
            <x-pagination/>
            {{-- Pagination --}}
        </div>
    </div>
    {{-- Modal for View All Products --}}

    {{-- Modal for Register New Product --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 p-5 lg:p-20" id="registerproductmodal">
        <div class="modal w-full lg:w-[50%] h-fit md:h-full m-auto rounded-lg bg-white p-10 relative">
            <span onclick="closeregisterproductmodal()" class="absolute text-6xl text-red-500 font-bold -right-4 -top-8 cursor-pointer">&times;</span>
            {{-- Form for register new product --}}
            <form id="addproduct" action="{{ route('admin.register.product') }}" method="POST" class="px-4">
                @csrf

                <h1 class="text-center font-bold text-4xl text-[#005382]">Register New Product</h1>
                <x-label-input label="Generic Name:" name="generic_name" type="text" for="generic_name" divclass="mt-5" placeholder="Enter Username"/>
                <x-label-input label="Brand Name:" name="brand_name" type="text" for="brand_name" divclass="mt-5" placeholder="Enter Account Name"/>
                <x-label-input label="Form:" name="form" type="text" id="form" for="form" placeholder="Enter Form (ex: Vials)" divclass="mt-5 relative"/>
                <x-label-input label="Strength:" name="strength" type="text" id="strength" for="strength" placeholder="Enter Strength (ex: 10mg/ml)" divclass="mt-5 relative"/>

                <x-submit-button id="addproductBtn"/>
            </form>
            {{-- Form for register new product --}}
        </div>
    </div>
    {{-- Modal for Register New Product --}}

    {{-- Add stock to specific product --}}
    <div id="addstock" class="bg-black/70 hidden fixed w-full h-full top-0 left-0 z-10 p-10">
        <div class="modal bg-white p-5 m-auto rounded-lg w-full lg:w-[40%] relative">
            <span class="absolute text-6xl font-bold text-red-600 cursor-pointer -right-4 -top-8" onclick="closeaddstock()">&times;</span>
            <h1 class="text-[#005382] text-xl font-bold">Add Stock in: <span class="text-black">Metoclopramide</span></h1>
            <form action="" id="addspecificstock">
                <x-label-input label="Batch Number:" name="batch_number" type="text" for="batch_number" divclass="mt-5" placeholder="Enter Batch Number"/>
                <x-label-input label="Quantity:" name="quantity" type="number" for="quantity" divclass="mt-5" placeholder="Enter Quantity"/>
                <x-label-input label="Expiry Date:" name="expiry_date" type="date" for="expiry_date" divclass="mt-5" placeholder="Enter Expiry Date"/>
                <x-submitbutton id="addstockBtn"/>
            </form>
        </div>
    </div>
    {{-- Add stock to specific product --}}

    {{--  Scan Receipt--}}
    <div class="addmodal hidden fixed bg-black w-full h-full top-0 left-0 px-[50px]" id="addmodal">
        <div class="modal addmodal-content relative bg-white w-full lg:w-[40%] p-5 rounded-lg mx-auto mt-20 flex flex-col md:flex-row gap-[40px]">
            <span class="close absolute -top-10 -right-4 text-red-600 font-bold text-[50px] cursor-pointer">&times;</span>
            {{-- drop file area --}}
            <div class="w-full lg:w-full h-full overflow-y-hidden">
                <h1 class="text-center text-[25px] font-bold">Upload Acknowledgment Receipt</h1>
                <p class="text-left">When the acknowledgment receipt is uploaded, the data is automatically inputted into the system.</p>
                <div class="drop-file flex flex-col items-center justify-center border-2 border-[#005382] h-[150px] lg:h-[290px] rounded-lg shadow-lg mt-2">
                    <img src="{{asset('image/image 49.png')}}" class="lg:w-[50px] w-[20px] mb-2">
                    <p class="lg:text-[20px] text-[15px]">Drop & Drop your files here</p>
                    <p class="text-[10px] lg:text-[15px] mb-2">or</p>
                    <input type="file" name="file" id="file" class="hidden">
                    <label for="file" class="px-[25px] py-1 bg-[#D9D9D9] rounded-lg cursor-pointer">Browse</label>
                </div>
            </div>
            {{-- drop file area --}}

        </div>
    </div>
    {{-- SCan Receipt --}}

    {{-- Add Multiple Stocks --}}
    <div id="addmultiplestock" class="hidden bg-black/70 w-full h-full left-0 top-0 p-10 pt-18 fixed">
        <div class="modal bg-white p-10 m-auto rounded-lg w-full lg:w-[40%] relative pb-20">
            <span onclick="closeaddmultiplestock()" class="absolute text-6xl font-bold text-red-600 cursor-pointer -right-4 -top-8">&times;</span>
            <h1 class="text-[#005382] font-bold text-xl">Add Multiple Stocks</h1>
            {{-- Form Multiple add stock--}}
            <form id="addmultiplestockform" action="{{ route('admin.inventory.store') }}" method="POST" class="w-full h-[50vh] p-2 overflow-y-auto z-1">  
                @csrf

                <div class="flex gap-2 mt-2">
                    <x-label-input label="Batch No:" name="batch_number" type="text" for="batch_number" divclass="w-1/2" inputclass="p-3"  placeholder="Enter Quantity"/>
                    <div class="flex flex-col w-1/2" inputclass="p-3"> {{-- TEMPORARY SOLUTION --}}
                        <label for="product_id" class="text-md font-semibold">Product:</label>
                        <select name="product_id" id="product_id" class="w-full p-3 outline-none rounded-lg" style="box-shadow:none; border: 1px solid #005382">
                            @if ($products->count() > 0)
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->generic_name ? $product->generic_name : "No Generic Name"}} - {{ $product->brand_name ? $product->brand_name : "No Brand Name" }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">No Products Listed</option>
                            @endif
                        </select>
                    </div> {{-- TEMPORARY SOLUTION --}}
                </div>
                <div class="flex gap-2 mt-2 mb-2">
                    <x-label-input label="Quantity:" name="quantity" type="text" for="quantity" divclass="w-1/2" inputclass="p-3" placeholder="Enter Quantity"/>
                    <x-label-input label="Expiry Date:" name="expiry_date" type="text" for="expiry_date" divclass="w-1/2" inputclass="p-3" placeholder="Enter Expiry Date"/>
                </div>
                <x-label-input label="Receipt Image:" name="img_file_path" type="file" for="img_file_path" inputclass="p-3" placeholder="Enter Expiry Date"/>

                {{-- Button for Save and Add more --}}
                <div class="flex absolute bottom-2 right-3 gap-4 mt-5 ">
                    <button id="addmore" type="button" 
                        class="flex items-center gap-2 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <i class="fa-solid fa-plus"></i> Add More
                    </button>
                    <button id="addmultiplestockBtn" type="button" 
                        class="flex items-center gap-2 bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 focus:ring-2 focus:ring-green-500 focus:outline-none">
                        <img src="{{asset('image/image 51.png')}}" alt="Save" class="h-5"> Save
                    </button>
                </div>
                {{-- Button for Save and Add more --}}
            </form>
        </div>
    </div>
</body>

<script src="{{asset('js/inventory.js')}}"></script>
<script src="{{asset ('js/sweetalert/inventorysweetalert.js')}}"></script>
</html>