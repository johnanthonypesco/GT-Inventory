<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{asset ('css/productlisting.css')}}">
    <title>Product Listing</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-[82%] md:w-full">
        <x-admin.header title="Product Listing Page" icon="fa-solid fa-list" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- All Products and Add New Product Button --}}
        <div class="mt-5 flex gap-5 justify-end">
            <button class="bg-white font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer" onclick="viewallproduct()"><i class="fa-regular fa-eye"></i>View All Products</button>
            <button class="bg-white font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer" onclick="registerproduct()"><i class="fa-solid fa-plus"></i>Register New Product</button>
        </div>
        {{-- All Products and Add New Product Button --}}

        <div class="w-full mt-5 bg-white p-5 rounded-lg">
            {{-- Customer List Search Function --}}
            <div class="flex flex-col md:flex-row justify-between items-center">
                <h1 class="font-bold text-2xl text-[#005382]">Customer List</h1>
                <x-input name="search" placeholder="Search Customer by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>        
            </div>
            {{-- Customer List Search Function --}}

            {{-- Table for customer List --}}
            <div class="table-container mt-5 overflow-auto h-[410px]">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Customer ID</th>
                            <th>Customer Name</th>
                            <th>Total Personalized Products</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1234</td>
                            <td>Jewel Velasquez</td>
                            <td>20 Personalized Products</td>
                            {{-- button for view and add --}}
                            <td class="m-auto flex gap-4 justify-center font-semibold">
                                <button class="cursor-pointer text-blue-500 py-1 rounded-lg" onclick="viewproductlisting()"><i class="fa-regular fa-eye mr-1"></i>View</button>
                                <button class="cursor-pointer text-green-500 py-1 rounded-lg" onclick="addproductlisting()"><i class="fa-regular fa-plus mr-1"></i>Add</button>
                            </td>
                            {{-- button for view and add --}}
                        </tr>
                        <tr>
                            <td>1234</td>
                            <td>Jewel Velasquez</td>
                            <td>20 Personalized Products</td>
                            {{-- button for view and add --}}
                            <td class="m-auto flex gap-4 justify-center font-semibold">
                                <button class="cursor-pointer text-blue-500 py-1 rounded-lg" onclick="viewproductlisting()"><i class="fa-regular fa-eye mr-1"></i>View</button>
                                <button class="cursor-pointer text-green-500 py-1 rounded-lg" onclick="addproductlisting()"><i class="fa-regular fa-plus mr-1"></i>Add</button>
                            </td>
                            {{-- button for view and add --}}
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Table for customer List --}}
        </div>
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
                    <tbody>
                        @foreach ($listedProducts as $product)
                            <tr>
                                <td>{{ $product->id}}</td>
                                <td>{{ $product->generic_name ?? "none" }}</td>
                                <td>{{ $product->brand_name ?? "none" }}</td>
                                <td>{{ $product->form }}</td>
                                <td>{{ $product->strength }}</td>
                                <td>
                                    buttons :)
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Table for all products --}}
        </div>
    </div>
    {{-- Modal for View All Products --}}

    {{-- Modal for Register New Product --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="registerproductmodal">
        <div class="modal w-full md:w-[50%] h-fit md:h-full m-auto rounded-lg bg-white p-10 relative">
            <span onclick="closeregisterproductmodal()" class="absolute text-6xl text-red-500 font-bold -right-4 -top-8 cursor-pointer">&times;</span>
            {{-- Form for register new product --}}
            <form action="{{ route('admin.register.product') }}" method="POST" class="px-4">
                @csrf

                <h1 class="text-center font-bold text-4xl text-[#005382]">Register New Product</h1>
                <div class="mt-5"> 
                    <label for="brand" class="text-gray-600/90 font-semibold text-xl tracking-wide">Brand Name:</label>
                    <input type="text" name="brand_name" placeholder="Enter Brand Name" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-2">
                </div>
                <div class="mt-2"> 
                    <label for="generic" class="text-gray-600/90 font-semibold text-xl tracking-wide">Generic Name:</label>
                    <input type="text" name="generic_name" placeholder="Enter Generic Name" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-2">
                </div>
                <div class="mt-2"> 
                    <label for="form" class="text-gray-600/90 font-semibold text-xl tracking-wide">Form:</label>
                    <input type="text" name="form" placeholder="Enter Form" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-2">
                </div>
                <div class="mt-2"> 
                    <label for="strength" class="text-gray-600/90 font-semibold text-xl tracking-wide">Strength:</label>
                    <input type="text" name="strength" placeholder="Enter Strength" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-2">
                </div>

                <button type="submit" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer"><img src="{{asset('image/image 51.png')}}" class="w-[20px]">Save</button>
            </form>
            {{-- Form for register new product --}}
        </div>
    </div>
    {{-- Modal for Register New Product --}}

    {{-- View Product Listing --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="viewproductlisting">
        <div class="modal w-full md:w-[80%] h-fit md:h-full m-auto rounded-lg bg-white p-10 relative">
            <span onclick="closeproductlisting()" class="absolute text-6xl text-red-500 font-bold -right-4 -top-8 cursor-pointer">&times;</span>
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-semibold text-[#005382]">Jewel Velasquez</h1>
                {{-- Button for Search --}}
                <div class="w-full md:w-[35%] relative">
                    <input type="search" placeholder="Search Product Name" class="w-full p-2 rounded-lg outline-none border border-[#005382]">
                    <button class="border-l-1 border-[#005382] px-3 cursor-pointer text-xl absolute right-2 top-2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div> 
                {{-- Button for Search --}}           
            </div>
            {{-- Table for all products --}}
            <div class="table-container mt-5 overflow-auto h-[80%]">
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
                                    <button class="text-[#005382] py-1 rounded-lg cursor-pointer flex items-center gap-1" onclick="editproductlisting()"><i class="fa-regular fa-pen-to-square"></i>Edit</button>
                                    <form action="">
                                        <button type="submit" class="text-red-500 py-1 rounded-lg cursor-pointer flex gap-1 items-center" onclick="return confirm('Are you sure you want to delete')"><i class="fa-solid fa-trash"></i>Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Table for all products --}}
        </div>
    </div>
    {{-- VIew Product Listing --}}

    {{-- Modal for Add Product Listing --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="addproductlisting">
        <div class="modal w-full md:w-[40%] h-full m-auto rounded-lg bg-white p-10 relative">
            <span onclick="closeaddproductlisting()" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">&times;</span>
            <form action="" class="h-[75%]">
                <h1 class="text-center font-bold text-3xl text-[#005382]">List New Product</h1>

                <div class="h-full overflow-auto">
                    <div>
                        <label for="customername" class="text-lg font-semibold text-black/80">Customer Name:</label>
                        <input type="text" name="customername" placeholder="Enter Customer Name" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-1">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2" id="addmoreproductlist">
                        <div>
                            <label for="product" class="text-lg font-semibold text-black/80">Product Name:</label>
                            <input type="text" name="product" placeholder="Enter Product Name" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-1">
                        </div>
                        <div>
                            <label for="productprice" class="text-lg font-semibold text-black/80">Product Price:</label>
                            <input type="text" name="productprice" placeholder="Enter Price Price" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-1">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between absolute bottom-0 w-full left-0 pb-5 h-fit px-10">
                    <button class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer" onclick="addmoreproductlisting()"><i class="fa-solid fa-plus"></i>Add More</button>
                    <button type="submit" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer"><img src="{{asset('image/image 51.png')}}" class="w-[20px]">Save</button>
                </div>
            </form> 
        </div>
    </div>
    {{-- Modal for Add Product Listing --}}

    {{-- Edit Product Listing --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="editproductlisting">
        <div class="modal w-full md:w-[40%] h-fit m-auto rounded-lg bg-white p-10 relative">
            <span onclick="closeeditproductlisting()" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">&times;</span>
            <form action="">
                <h1 class="text-center font-bold text-3xl text-[#005382]">Edit Product</h1>

                <div class="h-full overflow-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2" id="addmoreproductlist">
                        <div>
                            <label for="product" class="text-lg font-semibold text-black/80">Product Name:</label>
                            <input type="text" name="product" placeholder="Enter Product Name" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-1">
                        </div>
                        <div>
                            <label for="productprice" class="text-lg font-semibold text-black/80">Product Price:</label>
                            <input type="text" name="productprice" placeholder="Enter Price Price" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-1">
                        </div>
                    </div>
                </div>

                <button type="submit" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer"><img src="{{asset('image/image 51.png')}}" class="w-[20px]">Save</button>
            </form> 
        </div>
    </div>
</body>

<script src="{{asset('js/productlisting.js')}}"></script>
</html>