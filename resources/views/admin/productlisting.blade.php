<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{asset ('css/productlisting.css')}}">
    <script src="https://cdn.tailwindcss.com"></script>
            @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Product Listing</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full lg:ml-[16%]">
        <x-admin.header title="Product Deals" icon="fa-solid fa-list-check" name="John Anthony Pesco" gmail="admin@gmail"/>

        <div class="w-full mt-5 bg-white p-5 rounded-lg">
            {{-- Customer List Search Function --}}
            <div class="flex flex-col lg:flex-row justify-between items-center mb-5">
                <h1 class="font-bold text-2xl text-[#005382] ">Company List</h1>
                {{-- <x-input name="search" placeholder="Search Companies by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg "/>       --}}

                <div class="w-full lg:w-[50%] bg-white flex flex-col lg:flex-row gap-3 items-center rounded-lg p-2">

                @php
                    $hoverButtonEffect = 'hover:bg-[#005382] hover:text-white transition-all duration-200 hover:-mt-1 hover:mb-1 hover:shadow-lg';
                @endphp

                <button class="px-5 py-3 bg-white text-sm whitespace-nowrap w-fit font-semibold shadow-sm shadow-blue-400 rounded-lg uppercase flex items-center gap-2 cursor-pointer relative {{ $hoverButtonEffect }}" onclick="viewArchivedDeals()">
                    <i class="fa-solid fa-box-archive"></i>
                    View Archived Deals 
                </button>

                {{-- Datalist --}}
                <datalist id="company-search-suggestions">
                    @foreach ($companySearchSuggestions as $company)
                        <option value="{{ $company->name }}">
                    @endforeach
                </datalist>

                {{-- Reset Button --}}
                @if ($current_search["query"] !== null && $current_search["type"] === "company")
                    <button 
                        onclick="window.location.href = '{{ route('admin.productlisting') }}'" 
                        class="bg-red-500/80 text-white font-semibold shadow-sm px-4 py-2 rounded-lg uppercase flex items-center gap-2 w-full text-sm sm:w-[200px]">
                        <i class="fa-solid fa-xmark"></i> Reset Search
                    </button>
                @endif

                {{-- Search Form --}}
                <form action="{{ route('admin.productlisting') }}" method="GET" id="company-search-form" class="relative w-full">
                    <input type="hidden" name="search_type" value="company">

                    <input type="search" name="current_search" 
                        id="company-search"
                        placeholder="Search Companies by Name"
                        list="company-search-suggestions"
                        autocomplete="off"
                        value="{{ $current_search['query'] && $current_search['type'] === 'company' ? $current_search['query'] : '' }}"
                        class="w-full p-2 pr-10 border border-[#005382] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#005382]"
                    >

                    <button type="submit"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 border-l-2 border-r-0 border-t-0 border-b-0 border-[#005382] px-2 py-1 cursor-pointer">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>

            </div>
            {{-- Customer List Search Function --}}


            @foreach ($locations as $locationName => $companies)
                @php
                    $hasSearchedADeal = $current_search['deal_company'];
                @endphp

                @if (count($companies) <= 0)
                    @continue
                @endif

                {{-- Table for customer List --}}
                <h1 class="text-xl font-bold uppercase"> companies in {{ $locationName }}: </h1>
                <div class="table-container mb-8 overflow-auto h-fit h-max-[190px]">
                    <x-table
                    :headings="['Company ID', 'Company Name', 'Total Personalized Products', 'Action']" :variable="$companies" :secondaryVariable="$dealsDB"
                    :dealSearchCompany="$hasSearchedADeal"
                    category="productdeals"/>
                </div>
                {{-- Table for customer List --}}
            @endforeach
            {{-- pagination --}}
            {{-- <x-pagination/> --}}
            {{-- pagination --}}
        </div>
    </main>

    @php
        $uniqueProducts = $products->unique(function ($product) {
            return $product->generic_name . '|' . $product->brand_name . '|' . $product->form . '|' . $product->strength;
        });
    @endphp
    <datalist id="deal-search-suggestions">
        @foreach ($uniqueProducts as $product)
            <option value="{{ $product->generic_name }} - {{ $product->brand_name }} - {{ $product->form }} - {{ $product->strength }}">
        @endforeach
    </datalist>

    @foreach ($dealsDB as $companyName => $deals)
        {{-- mag repopup lang modal nato if nag edit, delete, paginate, search ka dun sa modal nayun --}}
        <div class="w-full {{ session('edit-success') && $companyName === session('company-success') || session("reSummon") === $companyName || request('reSummon') === $companyName || $current_search['deal_company'] === $companyName ? 'block' : 'hidden'}} h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="view-listings-{{ $companyName }}">
            
            <div class="modal w-full lg:w-[80%] h-fit md:h-full m-auto rounded-lg bg-white p-10 relative">
                <x-modalclose click="closeproductlisting" closeType="customer-deals" :variable="$companyName"/>
                <div class="flex flex-col lg:flex-row md:justify-between items-center">
                    <h1 class="text-3xl font-semibold text-[#005382]">
                        Exclusive Deals: {{ 
                            $companyName
                        }}
                    </h1>
                    {{-- Deal Search --}}
                    <div class="w-full lg:w-[40%] bg-white flex flex-col lg:flex-row items-center gap-3 p-2 rounded-lg">

                        {{-- Reset Button (only when applicable) --}}
                        @if ($current_search["query"] !== null && $current_search["type"] === "deal" && $current_search['deal_company'] === $companyName)
                            <button 
                                onclick="window.location.href = '{{ route('admin.productlisting') }}'"
                                class="bg-red-500/80 text-white font-semibold shadow-sm px-4 py-2 rounded-lg uppercase flex items-center gap-2 w-full sm:w-[250px] text-sm">
                                <i class="fa-solid fa-xmark"></i> Reset Search
                            </button>
                        @endif

                        @php
                            $formID = 'deal-search-form-' . str($companyName)->slug();
                            $inputID = 'deal-search-' . str($companyName)->slug();
                        @endphp

                        {{-- Search Form --}}
                        <form action="{{ route('admin.productlisting') }}" method="GET" id="{{ $formID }}" class="relative w-full">
                            <input type="hidden" name="search_type" value="deal">
                            <input type="hidden" name="specific_company_deal" value="{{ $companyName }}">

                            <input type="search" name="current_search" 
                                id="{{ $inputID }}"
                                placeholder="Search Product Deal"
                                list="deal-search-suggestions"
                                autocomplete="off"
                                value="{{ $current_search['query'] && $current_search['type'] === 'deal' && $current_search['deal_company'] === $companyName ? $current_search['query'] : '' }}"
                                class="w-full p-2 pr-10 border border-[#005382] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#005382]"
                                onkeydown="if(event.key === 'Enter') {
                                    isInSuggestionDeal('{{ $formID }}', '{{ $inputID }}') 
                                        ? this.form.submit() 
                                        : event.preventDefault();
                                }"
                            >

                            <button type="button"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 border-l-2 border-r-0 border-t-0 border-b-0 border-[#005382] px-1 py-1 cursor-pointer"
                                onclick="isInSuggestionDeal('{{ $formID }}', '{{ $inputID }}') 
                                    ? document.getElementById('{{ $formID }}').submit() 
                                    : event.preventDefault();">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </form>
                    </div>

                    {{-- Button for Search --}}
                </div>

                {{-- Table for all products --}}
                <div class="table-container mt-5 overflow-auto h-[50vh] lg:h-[76%]">
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
                        <tbody id="real-timer-deals-table" data-company="{{ $companyName }}">
                            @foreach ($deals->items() as $deal)
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
                                            route="admin.productlisting.archive" 
                                            deleteType="deleteDeal"
                                            :variable="$deal->company->name"
                                            method="PUT"
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
                {{-- <x-pagination/> --}}
                <div id="real-timer-paginate" data-company="{{ $companyName }}" class="mt-5">
                    {{ $deals->links() }}
                </div>
                {{-- Pagination --}}
            </div>
        </div>
    @endforeach
    {{-- VIew Product Listing --}}

    {{-- Modal for Add Product Listing --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="addproductlisting">
        <div class="modal w-full lg:w-[40%] h-full m-auto rounded-lg bg-white p-10 relative">
            <x-modalclose click="closeaddproductlisting"/>
            {{-- Form --}}
            <form action=" {{ route('admin.productlisting.create') }} " method="POST" class="h-[75%]" id="addproductlistingform">
                @csrf

                <h1 class="text-center font-bold text-3xl text-[#005382]">List New Product Deal</h1>

                <div class="h-full overflow-auto">
                    <input type="hidden" name="company_id" id="company-id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2 relative" id="addmoreproductlist">
                        <div>
                            <label for="product_id" class="text-md font-semibold">Select Product</label>
                            <select name="product_id[]" id="product_id" class="w-full p-[9.5px] outline-none border border-[#005382] rounded-lg">
                                @foreach ($uniqueProducts as $product)
                                    <option value="{{ $product->id }}">
                                        {{$product->generic_name}} - {{ $product->brand_name }} - {{ $product->form }} - {{ $product->strength }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-label-input label="Product's Price" name="price[]" type="number" for="price" placeholder="Enter Exclusive Price"/>
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
                $form = $deal->product->form ?? 'No Form';
                $strength = $deal->product->strength ?? 'No Strenth';
            @endphp

            <div class="w-full -mt-[4000px] h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="edit-listing-{{ $deal->id }}">
                <div class="modal w-full lg:w-[40%] h-fit m-auto rounded-lg bg-white p-10 relative">
                    <x-modalclose :variable="$deal->id" closeType="edit-product-deal" />
                    {{-- Form --}}
                    <form method="post" action="{{ route('admin.productlisting.update', ['aidee' => $deal->id]) }}" id="editproductlistingform">
                        @csrf
                        @method("PUT")

                        <div id="real-timer-deals-edit" 
                        data-deal="{{ $deal->id }}">
                            <h1 class="text-center font-bold text-3xl text-[#005382]">
                                Edit Product's Price: <br> {{ $generic }} - {{ $brand }} - {{ $form }} - {{ $strength }}
                            </h1>
    
                            <input type="hidden" value="{{ $deal->company->name }}" name="company">
    
                            <x-label-input label="Current Price:" value="{{ $deal->price }}" disabled name="current-price" type="number" for="current-price" divclass="mt-5" placeholder="Current Price"/>
                        </div>

                        <x-label-input label="New Price:" name="price" type="number" for="price" divclass="mt-5" placeholder="1500"/>

                        {{-- <x-label-input label="Product Name" name="price" type="text" for="brandname" divclass="mt-5" placeholder="Enter Account Name"/> --}}
                        <x-submit-button btnType="button" id="editproductlistingBtn"/>
                    </form> 
                    {{-- Form --}}
                </div>
            </div>
        @endforeach
    @endforeach

    {{-- ARCHIVED EXCLUSIVE DEAL POPUP MODAL --}}
    <div class="w-full {{ session("unarchived")  ? 'block' : 'hidden'}} h-full bg-black/70 fixed top-0 left-0 p-5 md:p-20" id="view-archived-listings">
        
        <div class="modal w-full lg:w-[80%] h-fit md:h-full m-auto rounded-lg bg-white p-10 relative">
            <x-modalclose click="viewArchivedDeals" closeType="customer-deals-archive"/>
                <h1 class="text-4xl uppercase font-semibold text-[#005382] mb-9">
                    Archived Exclusive Deals:
                </h1>
                
                <div class="flex-col h-[420px] overflow-scroll border-y-4 border-opacity-60 border-[#005382] p-1 py-2" id="real-timer-archived-deals-modal">
                    @foreach ($archivedDealsDB as $companyName => $deals)
                        @if ($deals->total() <= 0)
                            @continue
                        @endif
    
    
                        <h1 class="text-3xl font-semibold text-[#005382]">
                            Deals From: {{ 
                                $companyName
                            }}
                        </h1>
    
                        {{-- Table for all ARCHIVED DEALS --}}
                        <div class="table-container mt-5 overflow-auto h-fit">
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
                                    @foreach ($deals->items() as $deal)
                                    <tr class="text-center">
                                        <td>{{ $deal->product->generic_name }}</td>
                                        <td>{{ $deal->product->brand_name }}</td>
                                        <td>{{ $deal->product->form }}</td>
                                        <td>{{ $deal->product->strength }}</td>
                                        <td>₱ {{ number_format($deal->price) }}</td>
                                        <td class="flex justify-center">
                                            <form class="unarchiveform" action="{{ route('admin.productlisting.archive', [$deal->id, $deal->company->name, 'undo']) }}" method="post">
                                                @csrf
                                                @method('PUT')

                                                <button type="button" class="unarchivebtn flex gap-2 items-center text-[#005382] cursor-pointer font-bold">
                                                    <i class="fa-solid fa-undo"></i>
                                                    Unarchive
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- Table for all ARCHIVED products --}}
    
                        {{-- Archive Pagination --}}
                        <div class="mt-2">
                            {{ $deals->links() }}
                        </div>
                        {{-- Aarchive Pagination --}}

                        <div class="flex justify-center w-full gap-3 my-4">
                            <hr class="bg-[#005382] h-2 rounded-full w-[5%]">
                            <hr class="bg-[#005382] h-2 rounded-full w-[10%]">
                            <hr class="bg-[#005382] h-2 rounded-2xl w-[70%]">
                            <hr class="bg-[#005382] h-2 rounded-full w-[10%]">
                            <hr class="bg-[#005382] h-2 rounded-full w-[5%]">
                        </div>
                    @endforeach
                </div>
        </div>
    </div>
    {{-- ARCHIVED EXCLUSIVE DEAL POPUP MODAL --}}


    {{-- @if (session ('success'))
        <div id="successAlert" class="w3 fixed top-5 right-5 bg-green-500 text-white py-3 px-6 rounded-lg shadow-lg z-101 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-2xl"></i>
            <div>
                <p class="font-bold">Success!</p>
                <p id="successMessage"></p>
            </div>
        </div>
    @elseif (session ('error'))
        <div id="errorAlert" class="w3 fixed top-5 right-5 bg-red-500 text-white py-3 px-6 rounded-lg shadow-lg z-101 flex items-center gap-3">
        <div id="errorAlert" class="w3 fixed top-5 right-5 bg-red-500 text-white py-3 px-6 rounded-lg shadow-lg z-101 flex items-center gap-3">
            <i class="fa-solid fa-circle-xmark text-2xl"></i>
            <div>
                <p class="font-bold">Error!</p>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif
     --}}

    {{-- loader --}}
    <x-loader />
    {{-- loader --}}

     <x-successmessage />
</body>

<script src="{{asset('js/productlisting.js')}}"></script>
<script src="{{asset ('js/sweetalert/productlistingsweetalert.js')}}"></script>
<script src="{{asset('js/sweetalert/deletebuttonsweetalert.js')}}"></script>
<script>
    window.successMessage = @json(session('success'));

    document.addEventListener('DOMContentLoaded', function () {
        const totalPersonalCounterID = '#real-timer-total-personal-counter';
        const searchCompanyID = '#company-search-suggestions';
        
        const dealsTableID = '#real-timer-deals-table';
        const paginationID = '#real-timer-paginate';
        const searchDealID = '#deal-search-suggestions';
        const editDealFormID = '#real-timer-deals-edit';

        const archivedDealsModalID = '#real-timer-archived-deals-modal';
        // const archivedPaginationID = '#real-timer-archive-paginate';

        // every 5 secs mag update yung main section
        setInterval(() => {
            updateListingPage(window.location.href);
        }, 8500); 

        function updateListingPage(url) {
            fetch(url)
            .then(response => response.text()) // convert blade view to text
            .then(html => {
                const parser = new DOMParser();
                const updatedPage = parser.parseFromString(html, 'text/html');

                // DITO YUNG MULTI REPLACE SECTION
                const currentCounters = document.querySelectorAll(totalPersonalCounterID);
                const currentTables = document.querySelectorAll(dealsTableID);
                const currentPaginate = document.querySelectorAll(paginationID);
                const currentEditForms = document.querySelectorAll(editDealFormID);
                

                currentCounters.forEach(currentCounter => {
                    const company = currentCounter.dataset.company;

                    // Update the current iter with the updated version
                    const updatedCounter = updatedPage.querySelector(`${totalPersonalCounterID}[data-company="${company}"]`);
                    
                    if (updatedCounter) {
                        currentCounter.innerHTML = updatedCounter.innerHTML;
                    }
                });

                currentTables.forEach(currentTable => {
                    const company = currentTable.dataset.company;
                    const currentPaginateBTNS = document.querySelector(`${paginationID}[data-company="${company}"]`);

                    // Update the current iter with the updated version
                    const updatedTable = updatedPage.querySelector(`${dealsTableID}[data-company="${company}"]`);
                    const updatedPaginateBTNS = updatedPage.querySelector(`${paginationID}[data-company="${company}"]`);
                    
                    if (updatedTable) {
                        currentTable.innerHTML = updatedTable.innerHTML;
                        currentPaginateBTNS.innerHTML = updatedPaginateBTNS.innerHTML;
                    }
                });
                
                currentEditForms.forEach(currentEditForm => {
                    const deal = currentEditForm.dataset.deal;

                    // Update the current iter with the updated version
                    const updatedEditForm = updatedPage.querySelector(`${editDealFormID}[data-deal="${deal}"]`);
                    
                    if (updatedEditForm) {
                        currentEditForm.innerHTML = updatedEditForm.innerHTML;
                    }
                });
                
                // DITO YUNG MULTI REPLACE SECTION

                // DITO YUNG SINGULAR REPLACE SECTION
                const currentArchivedModal = document.querySelector(archivedDealsModalID);
                const updatedArchiveModal = updatedPage.querySelector(archivedDealsModalID);
                currentArchivedModal.innerHTML = updatedArchiveModal.innerHTML;

                const currentCompanySearch = document.querySelector(searchCompanyID);
                const updatedCompanySearch = updatedPage.querySelector(searchCompanyID);

                const currentCompanyOptions = Array.from(currentCompanySearch.options).map(opt => opt.value).join(',');
                const updatedCompanyOptions = Array.from(updatedCompanySearch.options).map(opt => opt.value).join(',');

                if (currentCompanyOptions !== updatedCompanyOptions) {
                    currentCompanySearch.innerHTML = updatedCompanySearch.innerHTML;
                    console.log("company search updated");
                }
                
                const currentDealSearch = document.querySelector(searchDealID);
                const updatedDealSearch = updatedPage.querySelector(searchDealID);

                const currentDealOptions = Array.from(currentDealSearch.options).map(opt => opt.value).join(',');
                const updatedDealOptions = Array.from(updatedDealSearch.options).map(opt => opt.value).join(',');

                if (currentDealOptions !== updatedDealOptions) {
                    currentDealSearch.innerHTML = updatedDealSearch.innerHTML;
                    console.log("product search updated");
                }

                // DITO YUNG SINGULAR REPLACE SECTION

                console.log("updated full page successfully");
            })
            .catch(error => {
                console.error("The realtime update para sa product listing is not working ya bitch! ", error);
            });
        }
    });
</script>
</html>
