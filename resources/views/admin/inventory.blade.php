<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <link rel="stylesheet" href="{{asset ('css/inventory.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Inventory</title>
</head>
<body class="flex flex-col md:flex-row gap-4 h-[100vh]">

    <x-admin.navbar/>

    <main class="md:w-full h-full">
        <x-admin.header title="Inventory" icon="fa-solid fa-boxes-stacked" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- Total Container --}}
        <div class="mt-3 grid grid-cols-2 lg:grid-cols-5 gap-2">
            <div class="item-container flex gap-5 sm:w-[190px] w-[150px] p-5 sm:h-[120px] rounded-lg bg-white relative">
                <div class="flex flex-col">
                    <p class="text-md sm:text-2xl">{{ $inStocks->count() ?? 0 }}</p>
                    <p class="font-bold mt-2">Products In Stock</p>
                    <x-stock-overview-btn  buttonType="in-stock" />
                </div>
                <img src="{{asset ('image/image.png')}}" class="absolute right-2 top-2">
            </div>

            <div class="item-container flex gap-5 sm:w-[190px] w-[150px] p-5 sm:h-[120px] rounded-lg bg-white relative">
                <div class="flex flex-col">
                    <p class="text-md sm:text-2xl">
                        {{
                            $lowStocks->count() ? $lowStocks->count() : 0
                        }}
                    </p>
                    <p class="font-bold mt-2">Low Stock Products</p>
                    <x-stock-overview-btn  buttonType="low-stock" />
                </div>
                <img src="{{asset ('image/image (1).png')}}" class="absolute right-2 top-2">
            </div>

            <div class="item-container flex gap-5 sm:w-[190px] w-[150px] p-5 sm:h-[120px] rounded-lg bg-white relative">
                <div class="flex flex-col">
                    <p class="text-md sm:text-2xl">
                        {{ 
                            $outOfStocks->count() ? $outOfStocks->count() : 0      
                        }}
                    </p>
                    <p class="font-bold mt-2">Products Out of Stock</p>
                    <x-stock-overview-btn  buttonType="out-stock" />
                </div>
                <img src="{{asset ('image/image (2).png')}}" class="absolute right-2 top-2">
            </div>
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

            <div class="flex gap-3 mt-1">
                <button class="bg-white font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer" onclick="viewallproduct()"><i class="fa-regular fa-eye"></i>View All Products</button>
                <button class="bg-white font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer" onclick="registerproduct()"><i class="fa-solid fa-plus"></i>Register New Product</button>
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
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr>
                            <th class="p-2 font-regular">Batch No.</th>
                            <th class="p-2 font-regular">Brand Name</th>
                            <th class="p-2 font-regular">Generic Name</th>
                            <th class="p-2 font-regular">Form</th>
                            <th class="p-2 font-regular">Stregth</th>
                            <th class="p-2 font-regular">Quantity</th>
                            <th class="p-2 font-regular">Expiry Date</th>
                            <th class="p-2 font-regular">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventories as $inv)
                            <tr class="text-center">
                                <td>{{ $inv->batch_number }}</td>
                                <td>{{ $inv->product->brand_name }}</td>
                                <td>{{ $inv->product->generic_name }}</td>
                                <td>{{ $inv->product->form }}</td>
                                <td>{{ $inv->product->strength }}</td>
                                <td>{{ $inv->quantity }}</td>
                                <td>{{ $inv->expiry_date }}</td>
                                <td class="{{ $inv->quantity < 100 ? "text-yellow-600 font-semibold" : "text-green-500"}}">{{ $inv->quantity < 100 ? "Low Stock" : "In Stock"}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Table for Inventory --}}

            {{-- Pagination --}}
            <x-pagination/>
            {{-- Pagination --}}
        </div>

        {{-- Add New modal --}}
        <div class="addmodal hidden fixed bg-black w-full h-full top-0 left-0 px-[50px] overflow-x-auto" id="addmodal">
            {{-- Modal Content --}}
            <div class="modal addmodal-content relative bg-white w-full md:w-[55%] h-full md:h-[500px] p-5 rounded-lg mx-auto mt-20 flex flex-col md:flex-row gap-[40px]">
                <span class="close absolute -top-10 -right-4 text-red-600 font-bold text-[50px] cursor-pointer">&times;</span>

                {{-- drop file area --}}
                <div class="w-full lg:w-[40%] h-full overflow-y-hidden">
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

                {{-- Form --}}
                <form action="{{ route('admin.inventory.store') }}" method="POST" id="addform" class="lg:w-[60%] w-full overflow-y-auto z-1 ">  
                    @csrf

                    <h1 class="text-[18px] text-[#005382] font-bold">Add New Stock</h1>

                    <div class="mt-5 grid grid-cols-2 gap-2">
                        <x-label-input label="Batch No:" name="batch_number" type="text" for="batch_number" divclass="" placeholder="Enter Quantity"/>
                        <div class="flex flex-col"> {{-- TEMPORARY SOLUTION --}}
                            <label for="product_id" class="text-[15px] font-semibold">Product:</label>
                            <select name="product_id" id="product_id" class="w-full p-3 outline-none rounded-lg mt-1" style="box-shadow:none; border: 1px solid #005382">
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
                        <x-label-input label="Quantity:" name="quantity" type="text" for="quantity" divclass="" placeholder="Enter Quantity"/>
                        <x-label-input label="Expiry Date:" name="expiry_date" type="text" for="expiry_date" divclass="" placeholder="Enter Expiry Date"/>
                        <x-label-input label="Receipt Image:" name="img_file_path" type="file" for="img_file_path" divclass="" placeholder="Enter Expiry Date"/>
                        <div></div>
                        <hr class="border-t border-black w-[410px] mt-5">
                    </div>

                    {{-- Button for Save and Add more --}}
                    <div class="modal-button flex justify-between absolute gap-2 bottom-2 right-5">
                        <button id="addmore" class="bg-white w-fit flex items-center gap-1"><i class="fa-solid fa-plus"></i>Add More</button>
                        <button onclick="return confirm('Are you sure you want to save?')" type="submit" class="bg-white w-fit flex items-center gap-1"><img src="{{asset('image/image 51.png')}}" class="w-[20px]">Save</button>
                    </div>
                    {{-- Button for Save and Add more --}}
                </form>

            </div>
            {{-- Modal Content --}}
        </div>
        {{-- Add New Modal --}}
    </main>

    {{-- Modal for View All Products --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="viewallproductmodal">
        <div class="modal w-full md:w-[80%] h-fit md:h-full m-auto rounded-lg bg-white p-10 relative">
            <span id="viewallproductclose" class="text-6xl font-bold text-red-600 cursor-pointer absolute -right-4 -top-8" onclick="closeviewallproduct()">&times;</span>
            {{-- Button for Search --}}
            <div class="flex justify-between items-center">
                <h1 class="font-bold text-2xl text-[#005382]">All Products</h1>
                <x-input name="search" placeholder="Search Product by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>                 
            </div>
            {{-- Button for Search --}}

            {{-- Table for all products --}}
            <div class="table-container mt-5 overflow-auto h-[400px]">
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
                                <td>
                                    <x-delete-button route="admin.destroy.product" routeid="{{$product->id}}" method="DELETE"
                                    {{-- 8===D ayaw naman gumana ng action mo <====8 gumawa nalang ako ibang props
                                    use action like this action="{{ route('admin.delete.product', $product->id) }}"
                                    and declare a method like this method="delete"

                                    @csrf and @method are already included in the deletebutton component
                                    "--}}
                                    />
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
            <form action="{{ route('admin.register.product') }}" method="POST" class="px-4">
                @csrf

                <h1 class="text-center font-bold text-4xl text-[#005382]">Register New Product</h1>
                <x-label-input label="Generic Name:" name="generic_name" type="text" for="generic_name" divclass="mt-5" placeholder="Enter Username"/>
                <x-label-input label="Brand Name:" name="brand_name" type="text" for="brand_name" divclass="mt-5" placeholder="Enter Account Name"/>
                <x-label-input label="Form:" name="form" type="text" id="form" for="form" placeholder="Enter Form (ex: Vials)" divclass="mt-5 relative"/>
                <x-label-input label="Strength:" name="strength" type="text" id="strength" for="strength" placeholder="Enter Strength (ex: 10mg/ml)" divclass="mt-5 relative"/>

                <x-submit-button/>
            </form>
            {{-- Form for register new product --}}
        </div>
    </div>
    {{-- Modal for Register New Product --}}
</body>

<script src="{{asset('js/inventory.js')}}"></script>
</html>