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
    <link rel="icon" href="{{ asset('image/Logowname.png') }}" type="image/png">
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{asset ('css/productlisting.css')}}">

    <script src="https://cdn.tailwindcss.com"></script>
    <title>Product Listing</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full md:ml-[16%]">
        <x-admin.header title="Product Deals" icon="fa-solid fa-list-check" name="John Anthony Pesco" gmail="admin@gmail"/>

        <div class="w-full mt-5 bg-white p-5 rounded-lg">
            {{-- Customer List Search Function --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-5">
                <h1 class="font-bold text-2xl text-[#005382]">Company List</h1>
                <x-input name="search" placeholder="Search Companies by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg "/>        
            </div>
            {{-- Customer List Search Function --}}


            @foreach ($locations as $locationName => $companies)
                {{-- Table for customer List --}}
                <h1 class="text-xl font-bold uppercase"> companies in {{ $locationName }}: </h1>
                <div class="table-container mb-8 overflow-auto h-fit h-max-[190px]">
                    <x-table
                    :headings="['Company ID', 'Company Name', 'Total Personalized Products', 'Action']" :variable="$companies" :secondaryVariable="$dealsDB"
                    category="productdeals"/>
                </div>
                {{-- Table for customer List --}}
            @endforeach
            {{-- pagination --}}
            <x-pagination/>
            {{-- pagination --}}
        </div>
    </main>

    {{-- @php
        dd($dealsDB['yahoo baby!']->first()->company->name);
    @endphp --}}


    {{-- View Product Listing --}}
    {{-- @if (session('edit-success'))
        @php
            dd('with works');
        @endphp
    @endif --}}
    @foreach ($dealsDB as $companyName => $deals)
        <div class="w-full {{ session('edit-success') && $companyName === session('company-success') ? 'block' : 'hidden'}} h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="view-listings-{{ $companyName }}">
            @if (session("reSummon"))
                <script>
                    addEventListener("DOMContentLoaded", () => {
                        const modalLoaded = document.getElementById("view-listings-{{ session('reSummon') }}");

                        if(modalLoaded) {
                            modalLoaded.classList.replace('hidden', 'block');
                        }
                    });
                </script>
            @endif

            <div class="modal w-full md:w-[80%] h-fit md:h-full m-auto rounded-lg bg-white p-10 relative">
                <x-modalclose click="closeproductlisting" closeType="customer-deals" :variable="$companyName"/>
                <div class="flex flex-col md:flex-row md:justify-between items-center">
                    <h1 class="text-3xl font-semibold text-[#005382]">
                        Exclusive Deals: {{ 
                            $companyName
                        }}
                    </h1>
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
                            <tr class="text-center">
                                <th>Generic Name</th>
                                <th>Brand Name</th>
                                <th>Form</th>
                                <th>Strength</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deals as $deal)
                            <tr class="text-center">
                                <td>{{ $deal->product->generic_name }}</td>
                                <td>{{ $deal->product->brand_name }}</td>
                                <td>{{ $deal->product->form }}</td>
                                <td>{{ $deal->product->strength }}</td>
                                <td>₱ {{ number_format($deal->price) }}</td>
                                <td>
                                    <div class="flex gap-3 items-center justify-center text-xl">
                                        <x-editbutton onclick="editProductListing(`{{ $deal->id }}`)"/>
                                        <x-deletebutton :routeid="$deal->id" 
                                            route="admin.productlisting.destroy" 
                                            deleteType="deleteDeal"
                                            :variable="$deal->company->name"
                                            method="DELETE"
                                        />
                                    </div>
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
    @endforeach
    {{-- VIew Product Listing --}}

    {{-- Modal for Add Product Listing --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="addproductlisting">
        <div class="modal w-full md:w-[40%] h-full m-auto rounded-lg bg-white p-10 relative">
            <x-modalclose click="closeaddproductlisting"/>
            {{-- Form --}}
            <form action=" {{ route('admin.productlisting.create') }} " method="POST" class="h-[75%]" id="addproductlistingform">
                @csrf

                <h1 class="text-center font-bold text-3xl text-[#005382]">List New Product</h1>

                <div class="h-full overflow-auto">
                    <input type="hidden" name="company_id" id="company-id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2 relative" id="addmoreproductlist">
                        <div>
                            <label for="product_id" class="text-md font-semibold">Select Product</label>
                            <select name="product_id" id="product_id" class="w-full p-[9.5px] outline-none border border-[#005382] rounded-lg">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{$product->generic_name}} - {{ $product->brand_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-label-input label="Product's Price" name="price" type="number" for="price" placeholder="Enter Exclusive Price"/>
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
    @foreach ($dealsDB as $companyName => $deals)
        @foreach ($deals as $deal)
            @php
                $generic = $deal->product->generic_name ?? 'No Generic Name';
                $brand = $deal->product->brand_name ?? 'No Brand Name';
            @endphp

            <div class="w-full -mt-[4000px] transition-all duration-200 h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="edit-listing-{{ $deal->id }}">
                <div class="modal w-full md:w-[40%] h-fit m-auto rounded-lg bg-white p-10 relative">
                    <x-modalclose :variable="$deal->id" closeType="edit-product-deal" />
                    {{-- Form --}}
                    <form method="post" action="{{ route('admin.productlisting.update', ['aidee' => $deal->id]) }}" id="editproductlistingform">
                        @csrf
                        @method("PUT")

                        <h1 class="text-center font-bold text-3xl text-[#005382]">
                            Edit Product's Price: {{ $generic }} - {{ $brand }}
                        </h1>

                        <input type="hidden" value="{{ $deal->company->name }}" name="company">

                        <x-label-input label="Current Price:" value="{{ $deal->price }}" disabled name="current-price" type="number" for="current-price" divclass="mt-5" placeholder="Current Price"/>
                        <x-label-input label="New Price:" name="price" type="number" for="price" divclass="mt-5" placeholder="1500"/>

                        {{-- <x-label-input label="Product Name" name="price" type="text" for="brandname" divclass="mt-5" placeholder="Enter Account Name"/> --}}
                        <x-submit-button btnType="submit" id="editproductlistingBtn"/>
                    </form> 
                    {{-- Form --}}
                </div>
            </div>
        @endforeach
    @endforeach
</body>

<script src="{{asset('js/productlisting.js')}}"></script>
<script src="{{asset ('js/sweetalert/productlistingsweetalert.js')}}"></script>
</html>
