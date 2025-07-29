<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{asset ('css/inventory.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">



    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <title>Inventory</title>
</head>
<body class="flex flex-col md:flex-row gap-4 h-[100vh]">

    <x-admin.navbar/>

    <main class="md:w-full h-full lg:ml-[16%]">
        <x-admin.header title="Inventory" icon="fa-solid fa-boxes-stacked" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- $stockMonitor['paracetamol']["inventories"] --}}

        @php
            // these variables will be used for the totals in the notifs & stock overview summaries
            $inStockProducts = [];
            $lowStockProducts = [];
            $noStockProducts = [];
            foreach ($stockMonitor as $provinceName => $groupedNames) {
                foreach ($groupedNames as $generalInfo) {
                    switch($generalInfo['status']) {
                        case ("in-stock" ):
                            array_push($inStockProducts, ['total' => $generalInfo['total'] , 'inventory' => $generalInfo['inventories'], 'province' => $provinceName]);
                            break;
                        case ("low-stock" ):
                            array_push($lowStockProducts, ['total' => $generalInfo['total'] , 'inventory' => $generalInfo['inventories'], 'province' => $provinceName]);
                            break;
                        case ("no-stock" ):
                            array_push($noStockProducts, ['total' => $generalInfo['total'] , 'inventory' => $generalInfo['inventories'], 'province' => $provinceName]);
                            break;
                        default:
                            false;
                        break;
                    }
                }
            }

            
            // $collect = collect($lowStockProducts)->groupBy(function ($pairs) {
            //     return $pairs['inventory']->map(function ($stocks) {
            //         return $stocks->location->province;
            //     });
            // });
            // dd(collect($lowStockProducts)->groupBy('province')->toArray());

            // dd($inStockProducts[0]['inventory'][0]->product->generic_name);
            // dd($inStockProducts[0]);
        @endphp


        <div class="h-[82vh] overflow-x-auto mt-4">
                {{-- Total Container --}}
                <div class="mt-3 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 lg:gap-2">
                    <x-totalstock :count="count($inStockProducts)" title="Currently In Stock" image="image.png" buttonType="in-stock" />
                    <x-totalstock :count="count($lowStockProducts)" title="Currently Low on Stock" image="stocks.png" buttonType="low-stock" />
                    <x-totalstock :count="count($noStockProducts)" title="Currently Out of Stock" image="outofstocks.png" buttonType="out-stock" />
                    
                    <x-totalstock :count="$expiryTotalCounts['nearExpiry']" title="Currently Near Expiration" image="stocks.png" buttonType="near-expiry-stock" />
                    <x-totalstock :count="$expiryTotalCounts['expired']" title="Currently Expired Stocks" image="outofstocks.png" buttonType="expired-stock" />
                </div>
                {{-- Total Container --}}
                
                {{-- Shows An Overview Modal for Certain Product Categories --}}
                <x-stock-overview-modal  modalType="in-stock" :variable="collect($inStockProducts)->groupBy('province')" />
                <x-stock-overview-modal  modalType="low-stock" :variable="collect($lowStockProducts)->groupBy('province')" />
                <x-stock-overview-modal  modalType="out-stock" :variable="collect($noStockProducts)->groupBy('province')" /> 
                
                <x-stock-overview-modal  modalType="near-expiry-stock" :variable="$expiredDatasets['nearExpiry']" /> 
                <x-stock-overview-modal  modalType="expired-stock" :variable="$expiredDatasets['expired']" /> 
                {{-- Shows An Overview Modal for Certain Product Categories --}}
                {{-- Filters Location --}}
        <div class="flex justify-between flex-col lg:flex-row mt-3">
            <form action="{{ route('admin.inventory.location') }}" method="POST">
                @csrf @method("POST")

                <select onchange="this.form.submit()" name="location" id="location" class="w-full md:w-fit border p-2 py-2 rounded-lg mt-10 sm:mt-2 h-10 text-center text-[#005382] font-bold bg-white outline-none">
                    <option value="all" @selected($current_inventory === "All")>All Delivery Locations</option>
                    <option value="Tarlac" @selected($current_inventory === "Tarlac")>Tarlac</option>
                    <option value="Nueva Ecija" @selected($current_inventory === "Nueva Ecija")>Nueva Ecija</option>
                </select>
            </form>

            <div class="flex gap-3 mt-2">
                <button class="bg-white text-sm font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer" onclick="viewallproduct()"><i class="fa-regular fa-eye"></i>View All Products</button>
                <button class="bg-white text-sm font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer" onclick="registerproduct()"><i class="fa-solid fa-plus"></i>Register New Product</button>
            </div>
        </div>
        {{-- Filters Location --}}

        {{-- CHECKS IF THE SEARCH RESULTS TURNED OUT EMPTY HANDED --}}
        {{-- @if ($inventories->isEmpty() && $currentSearch['type'] === "stock")
            <script> 
                alert("Nothing in Inventory Records Found. Reloading The Page!");
                
                setTimeout(() => {
                    window.location.href = '{{ route('admin.inventory') }}';                    
                }, 1000);
            </script>
        @endif --}}

        
        
        @foreach ($displayed_inventory_locations as $loc)
            @php
                $provinceName = $loc->province;

                // Grab the paginator for this location
                $groupedStocks = $inventories[$loc->province];

                // dd($groupedStocks->items());

            @endphp

        <div class="table-container bg-white mt-2 mb-5 p-3 px-6 rounded-lg">
            <h1 class="text-xl font-bold mb-5">
                Delivery Location: {{ $currentSearch['location'] !== "All" ? $currentSearch['location'] : $provinceName }}
            </h1>

            <div class="flex flex-wrap justify-between items-center">
                    @php
                        $uniqueProducts = $products->unique(function ($product) {
                            return $product->generic_name . '|' . $product->brand_name . '|' . $product->form . '|' . $product->strength;
                        });
                    @endphp

                    {{-- Search --}}
                    <x-input name="search"
                    placeholder="Search Product by Name"
                    classname="fa fa-magnifying-glass"
                    divclass="w-full lg:w-[40%] bg-white relative rounded-lg"
                    id="search-stock-{{$provinceName}}"
                    searchType="stock"
                    :dataList="$uniqueProducts"
                    :autofill="true"
                    :location_filter="$provinceName"
                    :currentSearch="$currentSearch['type'] === 'stock' ? $currentSearch['query'] : '' "/>

                    {{-- Search --}}

                    <div class="button flex items-center gap-3 mt-3 lg:mt-0 m-auto md:m-0">
                       <button onclick="window.location.href='{{ route('upload.receipt') }}?location={{ $provinceName }}'" class="flex items-center gap-1"><i class="fa-solid fa-plus"></i>Scan Receipt</button>
                        {{-- <button class="flex items-center gap-1"><i class="fa-solid fa-list"></i>Filter</button> --}}
                        <form action="{{ route('admin.inventory.export', ['exportType' => $provinceName]) }}" method="get">
                            @csrf

                            <button type="submit" class="flex items-center gap-1"><i class="fa-solid fa-download"></i>Export</button>
                        </form>
                    </div>
                </div>

                {{-- Table for Inventory --}}
                <div class="overflow-auto h-[290px] mt-5">
                    {{-- <x-table :headings="['Batch No.', 'Generic Name', 'Brand Name', 'Form', 'Stregth', 'Quantity', 'Expiry Date', 'Action']" :variable="$currentSearch['query'] !== null && $currentSearch['location'] !== 'All' ? $inventories : $inventory" category="inventory"/> --}}
                    <x-table :headings="['Batch No.', 'Generic Name', 'Brand Name', 'Form', 'Stregth', 'Quantity', 'Expiry Date', 'Action']" :variable="$groupedStocks->items()" category="inventory"/>
                </div>
                {{-- Table for Inventory --}}

                {{-- Pagination --}}
                {{-- <x-pagination/> --}}

                <div class="mt-6">
                    {{ $groupedStocks->links() }}
                </div>
                {{-- Pagination --}}
            </div>

            @if ($currentSearch['location'] !== 'All')
                @break
            @endif
        @endforeach
        </div>
    </main>

    {{-- Modal for View All Products --}}
    <div class="w-full {{ session('registeredProductSearch') || request()->has('registered_product_page') || session('editProductSuccess') ? '' : 'hidden' }} h-full bg-black/70 fixed top-0 left-0 p-10 md:p-20" id="viewallproductmodal">
        <div class="modal w-full lg:w-[80%] h-fit md:h-full m-auto rounded-lg bg-white p-10 relative">
            <x-modalclose id="viewallproductclose" click="closeviewallproduct"/>
            <h1 class="font-bold text-2xl text-[#005382]">All Registered Products</h1>
            
            <div class="flex justify-between flex-col lg:flex-row gap-5 mt-5">
                <button onclick="addmultiplestock()" class="bg-white w-fit font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer"><i class="fa-solid fa-plus"></i>Add Multiple Stocks</button>
                
                <div class="flex gap-2 w-full lg:w-[420px]">
                    @if (session('registeredProductSearch'))
                        <button onclick="window.location.href = '{{route('admin.inventory')}}'" class="bg-red-500/80 w-fit text-white font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer">                         
                            Reset 
                        </button>
                    @endif

                    <x-input name="search"
                    placeholder="Search Product by Name"
                    classname="fa fa-magnifying-glass"
                    divclass="w-full lg:w-[100%] bg-white relative rounded-lg"
                    id="search-product"
                    searchType="product"
                    :dataList="$uniqueProducts"
                    :autofill="true"
                    :currentSearch="$currentSearch['type'] === 'product' ? $currentSearch['query'] : ''  "/>
                </div>
            </div>

            {{-- Table for all products --}}
            <div class="table-container mt-5 overflow-auto h-[300px]">
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
                        @foreach ($registeredProducts as $product)
                            @php
                                $generic_name = $product->generic_name ? $product->generic_name : "none";
                                $brand_name = $product->brand_name ? $product->brand_name : "none";
                            @endphp

                            <tr>
                                <td>{{ $product->id}}</td>
                                <td>{{ $generic_name }}</td>
                                <td>{{ $brand_name }}</td>
                                <td>{{ $product->form }}</td>
                                <td>{{ $product->strength }}</td>
                                <td class="flex items-center gap-4 justify-center">
                                    <button onclick="addstock('{{ $product->id }}', '{{ $generic_name }} - {{ $brand_name }}')" class="cursor-pointer flex items-center gap-2 text-[#005382]"><i class="fa-solid fa-plus"></i>Add Stock</button>

                                    <button class="flex items-center text-[#005382] cursor-pointer" onclick="editRegisteredProduct('{{$product->id}}', '{{$generic_name}}', '{{$brand_name}}', '{{$product->form}}', '{{$product->strength}}', '{{ url('/') }}/', '{{ $product->img_file_path }}')">
                                        <i class="fa-regular fa-pen-to-square mr-2"></i>
                                        Edit
                                    </button>

                                    <x-delete-button route="admin.destroy.product" routeid="{{$product->id}}" method="DELETE" id="delete"/>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Table for all products --}}

            {{-- Pagination --}}
            <div class="mt-5">
                {{ $registeredProducts->links() }}
            </div>
            {{-- Pagination --}}
        </div>
    </div>
    {{-- Modal for View All Products --}}

    {{-- Modal for Register New Product --}}
    @php
        $failedToRegister = $errors->hasAny(['generic_name', 'brand_name', 'form', 'strength']);
    @endphp

    <div class="w-full {{ $failedToRegister && old('form_type') !== 'edit-product' ? '' : 'hidden' }} h-full bg-black/70 fixed top-0 left-0 z-50 p-4 sm:p-6 md:p-10 lg:p-20 overflow-auto" id="registerproductmodal">
    <div class="modal w-full max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-6 sm:p-8 relative">
        <x-modalclose click="closeregisterproductmodal" />

        <!-- Register New Product Form -->
        <form id="addproduct" action="{{ route('admin.register.product') }}" method="POST" enctype="multipart/form-data" class="overflow-y-auto max-h-[90vh]">
            @csrf

            <h1 class="text-center font-bold text-2xl sm:text-3xl lg:text-4xl text-[#005382] mb-4">Register New Product</h1>

            {{-- BTW FORM_TYPE IS FOR THE EDIT REGISTERED PRODUCT'S ERROR HANDLING --}}
            <x-label-input label="Generic Name:" name="generic_name" type="text" for="generic_name" divclass="mt-4" placeholder="Enter Generic Name" value="{{ old('form_type') !== 'edit-product' ? old('generic_name') : '' }}" :errorChecker="old('form_type') !== 'edit-product' ? $errors->first('generic_name') : null"/>

            <x-label-input label="Brand Name:" name="brand_name" type="text" for="brand_name" divclass="mt-4" placeholder="Enter Brand Name" value="{{ old('form_type') !== 'edit-product' ? old('brand_name') : '' }}" :errorChecker="old('form_type') !== 'edit-product' ? $errors->first('brand_name') : null"/>

            <x-label-input label="Form:" name="form" type="text" id="form" for="form" divclass="mt-4" placeholder="Enter Form (ex: Vials)" value="{{ old('form_type') !== 'edit-product' ? old('form') : '' }}" :errorChecker="old('form_type') !== 'edit-product' ? $errors->first('form') : null"/>

            <x-label-input label="Strength:" name="strength" type="text" id="strength" for="strength" divclass="mt-4" placeholder="Enter Strength (ex: 10mg/ml)" value="{{ old('form_type') !== 'edit-product' ? old('strength') : '' }}" :errorChecker="old('form_type') !== 'edit-product' ? $errors->first('strength') : null"/>

            <!-- File Upload -->
            <div class="mt-4">
                <label for="img_file_path" class="text-black/80 font-semibold text-md">Product Picture (Optional):</label>
                <input type="file" accept=".jpeg,.jpg,.png" name="img_file_path" id="img-file-path"
                    class="mt-1 block w-full rounded-lg border border-[#005382] bg-white px-4 py-2 text-gray-700 placeholder-gray-400 focus:border-[#005382] focus:ring focus:ring-[#005382]/50 transition duration-150 ease-in-out">
                @error('img_file_path')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <x-submit-button id="addproductBtn" class="mt-6" />
        </form>
    </div>
</div>

    {{-- Modal for Register New Product --}}

    {{-- Add stock to specific product --}}
    @php
        $failedToAddStock = session('stockFailType') === 'single' ? true : false;
    @endphp

    <div id="addstock" class="bg-black/70 {{ $failedToAddStock ? '' : 'hidden'}} fixed w-full h-full top-0 left-0 z-10 p-10">
        <div class="modal bg-white p-5 m-auto rounded-lg w-full lg:w-[40%] relative">
            <x-modalclose click="closeaddstock"/>
            <h1 class="text-[#005382] text-xl font-bold">
                Add Stock in: <span id="single_add_name" class="text-black"> Current Product </span>
            </h1>
            <form action="{{ route('admin.inventory.store', ['addType' => 'single']) }}" method="POST" id="addspecificstock">
                @csrf

                <input type="hidden" id="single_product_id" value="{{ $failedToAddStock ? old('product_id.0') : '' }}" name="product_id[]">

                <div class="flex flex-col">
                    <label for="location_id[]">Delivery Location: {{ $errors->first('location_id') }}</label>
                    <select name="location_id[]" id="single_location_id" class="w-fit p-3 outline-none rounded-lg">
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->province }}</option>
                        @endforeach
                    </select>
                </div>
                <x-label-input label="Batch Number:" id="single_batch_number" name="batch_number[]" type="text" for="batch_number" divclass="mt-5" placeholder="Enter Batch Number" value="{{ $failedToAddStock ? old('batch_number.0') : '' }}" errorChecker="{{ $failedToAddStock ? $errors->first('batch_number.0') : null}}" />
                <x-label-input label="Quantity:" id="single_quantity" name="quantity[]" type="number" for="quantity" divclass="mt-5" placeholder="Enter Quantity" value="{{ $failedToAddStock ? old('quantity.0') : '' }}" errorChecker="{{ $failedToAddStock ? $errors->first('quantity.0') : null }}" />
                <x-label-input label="Expiry Date:" id="single_expiry" name="expiry_date[]" type="date" for="expiry_date" divclass="mt-5" placeholder="Enter Expiry Date" value="{{ $failedToAddStock ? old('quantity.0') : '' }}" errorChecker="{{ $failedToAddStock ? $errors->first('expiry_date.0') : null }}" />
                <x-submitbutton id="addstockBtn"/>
            </form>
        </div>
    </div>
    {{-- Add stock to specific product --}}


    {{-- Add Multiple Stocks --}}
    @php
        $addMultiStockFailed = session('stockFailType') === 'multiple' ? true : false;
    @endphp

    <div id="addmultiplestock" class="{{ $addMultiStockFailed ? '' : 'hidden'}} bg-black/70 w-full h-full left-0 top-0 p-10 pt-18 fixed overflow-auto">
        <div class="modal bg-white p-10 m-auto rounded-lg w-full lg:w-[40%] relative pb-20">
            <x-modalclose click="closeaddmultiplestock"/>
            <h1 class="text-[#005382] font-bold text-xl">Add Multiple Stocks</h1>

            {{-- DISPLAY ERRORS MODAL --}}
            @if ($addMultiStockFailed)
                <div class="bg-white p-5 m-auto rounded-lg w-fit z-50 border-1 border-rose-400 animate-pulse">
                    @php
                        $hasBeenPrinted = [];
                    @endphp

                    @foreach ($errors->all() as $error)
                        @php
                            // will create a string with no underscores or any indexes attached
                            $cleanedString = preg_replace(['/[_]/', '/\.\d+/'], ' ', $error);
                        @endphp

                        @if (in_array($cleanedString, $hasBeenPrinted)) {{-- skips the printed errors --}}
                            @continue
                        @endif

                        <p class="text-rose-600">
                            {{$cleanedString}}
                        </p>

                        @php // pushes the string in the array
                            $hasBeenPrinted[] = $cleanedString;
                        @endphp
                    @endforeach
                </div>
            @endif
            {{-- DISPLAY ERRORS MODAL --}}


            {{-- Form Multiple add stock--}}
            <form id="addmultiplestockform" action="{{ route('admin.inventory.store', ['addType' => 'multiple']) }}" method="POST" class="w-full h-[50vh] p-2 overflow-y-auto z-1">
                @csrf

                <div class="flex flex-col">
                    <label for="location_id[]">Delivery Location: {{ $errors->first('location_id') }}</label>
                    <select name="location_id[]" id="single_location_id" class="w-fit p-[12.5px] outline-none rounded-lg">
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->province }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2 mt-2">

                    <x-label-input label="Batch No:" name="batch_number[]" type="text" for="batch_number" divclass="w-1/2" inputclass="p-3"  placeholder="Enter Quantity"/>
                    <div class="flex flex-col w-1/2" inputclass="p-3">
                        <label for="product_id" class="text-md font-semibold">Product:</label>
                        <select name="product_id[]" id="product_id" class="w-full p-3 outline-none rounded-lg" style="box-shadow:none; border: 1px solid #005382">
                            @if ($products->count() > 0)
                                @foreach ($uniqueProducts as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->generic_name ? $product->generic_name : "No Generic Name"}} - {{ $product->brand_name ? $product->brand_name : "No Brand Name" }}  - {{ $product->form ? $product->form : "No Form" }} - {{ $product->strength ? $product->strength : "No Strength" }}
                                    </option>
                                @endforeach
                            @else
                                <option disabled value="">No Products Listed</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 mt-2 mb-2">
                    <x-label-input label="Quantity:" name="quantity[]" type="number" for="quantity" divclass="w-1/2" inputclass="p-3" placeholder="Enter Quantity"/>
                    <x-label-input label="Expiry Date:" name="expiry_date[]" type="date" for="expiry_date" divclass="w-1/2" inputclass="p-3" placeholder="Enter Expiry Date"/>
                </div>
                {{-- <x-label-input label="Receipt Image:" name="img_file_path[]" type="file" for="img_file_path" inputclass="p-3" placeholder="Enter Expiry Date"/> --}}

                {{-- template for adding more forms --}}
                <template id="stock-entry-template" data-current-clones="0">
                    <div class="w-full my-3 bg-blue-600 rounded-md py-1" ></div>

                    <div class="flex gap-2 mt-2">
                        <div>
                            <label for="location_id[]">Delivery Location: {{ $errors->first('location_id') }}</label>
                            <select name="location_id[]" id="single_location_id" class="w-fit p-3 outline-none rounded-lg">
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->province }}</option>
                                @endforeach
                            </select>
                        </div>

                        <x-label-input label="Batch No:" name="batch_number[]" type="text" for="batch_number" divclass="w-1/2" inputclass="p-3"  placeholder="Enter Quantity"/>
                        <div class="flex flex-col w-1/2" inputclass="p-3">
                            <label for="product_id" class="text-md font-semibold">Product:</label>
                            <select name="product_id[]" id="product_id" class="w-full p-3 outline-none rounded-lg" style="box-shadow:none; border: 1px solid #005382">
                                <option value="1" class="text-black/40" disabled selected>Select a Product</option>
                                @if ($products->count() > 0)
                                    @foreach ($uniqueProducts as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->generic_name ? $product->generic_name : "No Generic Name"}} - {{ $product->brand_name ? $product->brand_name : "No Brand Name" }} - {{ $product->form ? $product->form : "No Form" }} - {{ $product->strength ? $product->strength : "No Strength" }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled value="">No Products Listed</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-2 mb-2">
                        <x-label-input label="Quantity:" name="quantity[]" type="text" for="quantity" divclass="w-1/2" inputclass="p-3" placeholder="Enter Quantity"/>
                        <x-label-input label="Expiry Date:" name="expiry_date[]" type="date" for="expiry_date" divclass="w-1/2" inputclass="p-3" placeholder="Enter Expiry Date"/>
                    </div>
                    {{-- <x-label-input label="Receipt Image:" name="img_file_path[]" type="file" for="img_file_path" inputclass="p-3" placeholder="Enter Expiry Date"/> --}}
                </template>
                {{-- template for adding more forms --}}

                {{-- dito ipapasok yung mga bagong forms --}}
                <div id="template-container"></div>
                {{-- dito ipapasok yung mga bagong forms --}}

                {{-- Button for Save and Add more --}}
                <div class="flex absolute bottom-2 right-3 gap-4 mt-5 ">
                    <button id="addmore" type="button"
                        onclick="add_more_stocks_input(1)"
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


    {{-- EDIT REGISTERED PRODUCTS MODAL --}}
    @php
        $errorPresentInEdit = old('form_type') === 'edit-product';
    @endphp
    <div class="w-full {{ $errorPresentInEdit && $errors->any() ? '-mt-[0px]' : '-mt-[4000px]' }} transition-all duration-200 h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20 overflow-y-scroll" id="edit-registered">        
        <div class="modal w-full md:w-[40%] h-fit m-auto rounded-lg bg-white p-10 relative">
            <x-modalclose click="editRegisteredProduct" />

            <form action="{{ route('admin.edit.product') }}" method="POST" id="edit-prod-reset" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="flex justify-between">
                    <div class="flex-col gap-5">
                        <h1 id="title-prod-edit" class="font-bold text-2xl text-[#005382]"> Updating Product ID: {{ $errorPresentInEdit ? old("id")  : '' }} </h1>
                        <button type="submit" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer"> 
                            <img src="{{asset('image/image 51.png')}}"/>
                            Save Changes 
                        </button>
                    </div>

                    <img src="/" class="w-[128px] h-[128px] bg-black/60 object-scale-down self rounded-sm border-2 border-[#005382]" alt="product image" id="prod-img">
                </div>

                <input type="hidden" name="id" id="edit-prod-id" value="{{ $errorPresentInEdit ? old('id') : '' }}">
                <input type="hidden" name="form_type" value="edit-product">
                

                <x-label-input label="Generic Name:" inputid="edit-prod-generic" name="generic_name" type="text" for="generic_name" divclass="mt-2" placeholder="Enter Generic Name" value="{{ old('generic_name') }}"  :errorChecker="$errorPresentInEdit ? $errors->first('generic_name') : null " />
                    
                <x-label-input label="Brand Name:" inputid="edit-prod-brand" name="brand_name" type="text" for="brand_name" divclass="mt-5" placeholder="Enter Brand Name" value="{{ old('brand_name')}}" :errorChecker="$errorPresentInEdit ? $errors->first('brand_name') : null" />

                <x-label-input label="Form:" inputid="edit-prod-form" name="form" type="text" for="form" divclass="mt-5" placeholder="Enter Form" value="{{  old('form') }}" :errorChecker="$errorPresentInEdit ? $errors->first('form') : null" />

                <x-label-input label="Strength:" inputid="edit-prod-strength" name="strength" type="text" for="strength" divclass="mt-5" placeholder="Enter strength" value="{{ old('strength') }}" :errorChecker="$errorPresentInEdit ? $errors->first('strength') : null" />

                <div class="mt-5 relative">
                    <label for="img_file_path" class="text-black/80 font-semibold text-md tracking-wide">
                        Product Picture (Optional):
                    </label>
                    <input type="file" accept=".jpeg,.jpg,.png" name="img_file_path" id="edit-prod-img" class="mt-1 block w-full rounded-lg border border-[#005382] bg-white px-4 py-3 text-gray-700 placeholder-gray-400 focus:border-[#005382] focus:ring focus:ring-[#005382]/50 focus:outline-none transition duration-150 ease-in-out">
                    
                    @error('img_file_path')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </form>
        </div>
    </div>
    {{-- EDIT REGISTERED PRODUCTS MODAL --}}

    {{-- EDIT STOCK MODAL --}}
    @php
        $errorPresentInStockEdit = old('form_type') === 'edit-stock';
    @endphp
    <div class="w-full {{ $errorPresentInStockEdit && $errors->any() ? '-mt-[0px]' : '-mt-[4000px]'}}   transition-all duration-200 h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20 overflow-y-scroll" id="edit-stock">        
        <div class="modal w-full md:w-[40%] h-fit m-auto rounded-lg bg-white p-10 relative">
            <x-modalclose click="openStockEditModal" />

            <form action="{{ route('admin.edit.stock') }}" method="POST" id="edit-stock-form">
                @csrf
                @method('PUT')

                <div class="flex justify-between">
                    <h1 id="title-prod-edit" class="font-bold text-2xl text-[#005382]"> 
                        Edit Stock: <span id="chizburger"></span> 
                    </h1>
                </div>

                <input type="hidden" name="form_type" value="edit-stock">
                <input type="hidden" name="inventory_id" id="edit-stock-id" 
                value="{{ $errorPresentInStockEdit ? old('inventory_id') : '' }}">
                {{-- <input type="hidden" name="form_type" value="edit-product"> --}}
                

                <x-label-input label="Batch Number:" inputid="edit-stock-batch" name="batch_number" type="text" for="batch_number" divclass="mt-2" placeholder="Enter Batch Number" 
                value="{{ $errorPresentInStockEdit ? old('batch_number') : '' }}"
                :errorChecker="$errorPresentInStockEdit ? $errors->first('batch_number') : null " />
                    
                <x-label-input label="Quantity:" inputid="edit-stock-quantity" name="quantity" type="number" for="quantity" divclass="mt-5" placeholder="Enter Quantity" 
                value="{{ $errorPresentInStockEdit ? old('quantity') : '' }}"
                :errorChecker="$errorPresentInStockEdit ? $errors->first('quantity') : null "/>

                <x-label-input label="Expiry Date:" inputid="edit-stock-expiry" name="expiry_date" type="date" for="expiry_date" divclass="mt-5" 
                value="{{ $errorPresentInStockEdit ? old('expiry_date') : '' }}"
                :errorChecker="$errorPresentInStockEdit ? $errors->first('expiry_date') : null "/>

                <button type="submit" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer"> 
                    <img src="{{asset('image/image 51.png')}}"/>
                    Save Changes 
                </button>
            </form>
        </div>
    </div>
    {{-- EDIT STOCK MODAL --}}


   {{-- Transfer Inventory Modal --}}
<div id="transferInventoryModal" class="hidden fixed w-full h-full top-0 left-0 bg-black/70 ">
    <div class="modal bg-white p-6 rounded-lg w-full max-w-md m-auto mt-10">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Transfer Inventory</h2>
        
        <form id="transferForm">
            <input type="hidden" name="inventory_id" id="transfer_inventory_id">
            
            <p class="text-gray-600 mb-2">Batch Number: <span id="transfer_batch_number"></span></p>
            <p class="text-gray-600 mb-2">Product: <span id="transfer_product_name"></span></p>
            <p class="text-gray-600 mb-2">Current Location: <span id="transfer_current_location"></span></p>

            <label for="new_location" class="text-gray-600">New Location</label>
            <select id="new_location" name="new_location" class="w-full border rounded-md px-3 py-2 mt-2">
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->province }}</option>
                @endforeach
            </select>

            <div class="flex justify-between mt-4">
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-md" onclick="closeTransferModal()">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Confirm Transfer</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/inventory.js') }}"></script>
<script src="{{ asset('js/sweetalert/inventorysweetalert.js') }}"></script>

<script>
function openTransferModal(inventoryId, batchNumber, productName, currentLocation) {
    document.getElementById('transfer_inventory_id').value = inventoryId;
    document.getElementById('transfer_batch_number').textContent = batchNumber;
    document.getElementById('transfer_product_name').textContent = productName;
    document.getElementById('transfer_current_location').textContent = currentLocation;

    document.getElementById('transferInventoryModal').classList.remove('hidden'); // Show modal
}

function closeTransferModal() {
    document.getElementById('transferInventoryModal').classList.add('hidden'); // Hide modal
}
document.getElementById('transferForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = {
        inventory_id: document.getElementById('transfer_inventory_id').value,
        new_location: document.getElementById('new_location').value, // Ensure this is an `id` from `locations`
    };

    fetch("{{ route('admin.inventory.transfer') }}", {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Content-Type": "application/json"
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire("Success", data.message, "success")
                .then(() => window.location.reload()); // Reload to reflect changes
        } else {
            Swal.fire("Error", data.message || "Transfer failed.", "error");
        }
    })
    .catch(() => Swal.fire("Error", "Failed to connect to the server.", "error"));
});

</script>
</body>
</html>
