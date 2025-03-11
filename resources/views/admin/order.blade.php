@php
    use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/order.css') }}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Orders</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full md:ml-[16%]">
        <x-admin.header title="Orders" icon="fa-solid fa-cart-shopping" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- Total Container --}}
        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            <x-countcard title='Total Orders This Week' image="stocks.png" :count="$ordersThisWeek"/>
            <x-countcard title='Pending Orders' image="pending.png" :count="$currentPendings"/>
            <x-countcard title='Partially Delivered Orders' image="complete.png" :count="$currentPartials"/>
        </div>
        {{-- Total Container --}}

        <div class="h-[60vh] overflow-auto mt-8">
            @foreach ($provinces as $provinceName => $companies)
            {{-- Table for Order --}}
            <h1 class="text-[20px] sm:text-[30px] font-regularfont-bold">
                <span class="text-[#005382] text-[30px] font-bold mr-2">
                    Orders In:
                    {{ $provinceName }}
                </span>
            </h1>
            <div class="table-container bg-white p-5 rounded-lg">
                <div class="flex flex-wrap justify-between items-center">
                    {{-- Search --}}
                    <x-input name="searchconvo"
                            placeholder="Search Customer by Name"
                            classname="fa fa-magnifying-glass"
                            divclass=" w-full lg:w-[40%] bg-white relative rounded-lg"/>
                    {{-- Table Button --}}
                    <div class="table-button flex gap-4 mt-5 lg:mt-0">
                        <button onclick="uploadqr()">
                            <i class="fa-solid fa-upload"></i> Upload QR Code
                        </button>
                            <button onclick="window.location.href='{{ route('orders.scan') }}'">
                        <i class="fa-solid fa-qrcode"></i> Scan
                        </button>
                        <button>
                            <i class="fa-solid fa-download"></i>
                            Export
                        </button>
                    </div>
                    {{-- Table Button --}}
                </div>
                <select name="company" class="rounded-lg px-4 py-2 outline-none mt-5" style="box-shadow: 0 0 5px #00528288;">
                    <option value="company">All Company</option>
                    @foreach ($companies as $companyName => $employees)
                        <option value="{{ $companyName }}">{{ $companyName }}</option>
                    @endforeach
                </select>

                @foreach ($companies as $companyName => $employees)
                    
                    <h1 class="text-[20px] sm:text-[20px] font-regular mt-6 font-bold">
                        <span class="text-[#005382] text-[20px] font-bold mr-2">
                            Orders From:
                        </span>
                        {{ $companyName }}
                    </h1>

                    <div class="overflow-auto max-h-[200px] h-fit mt-5">
                        {{-- Table --}}
                        <x-table 
                            :headings="['Employee Name', 'Date Ordered', 'Action']" 
                            :variable="$employees"
                            category="order"
                        />
                        {{-- Table --}}
                    </div>
                    @endforeach
                    <x-pagination/>
            </div>
            {{-- Table for Order --}}
        @endforeach
        </div>

        {{-- View Order Modal --}}
        @foreach ($provinces as $companies)
            @foreach ($companies as $employees)
                @foreach ($employees as $employeeNameAndDate => $groupedStatuses)
                    @php
                        $total = 0;
                    @endphp
                    <div class="order-modal hidden fixed top-0 left-0 pt-[5px] w-full h-full 
                                items-center justify-center px-4"
                        id="order-modal-{{ $employeeNameAndDate }}">
                        <div class="modal order-modal-content mx-company w-full lg:w-[70%] bg-white p-5 
                                    rounded-lg relative shadow-lg">
                            {{-- Close button, etc. --}}
                            <x-modalclose click="closeOrderModal('{{ $employeeNameAndDate }}')"/>

                            <h1 class="text-xl font-bold uppercase mb-6">
                                @php 
                                    $separatedInModal = explode('|', $employeeNameAndDate);  
                                @endphp
                                Orders By: 
                                <span class="text-blue-800"> 
                                    {{ $separatedInModal[0] }} -
                                    [ {{ Carbon::parse($separatedInModal[1])->translatedFormat('M d, Y') }} ]
                                </span>
                            </h1>
                            
                            <div class="table-container h-[360px] overflow-y-auto">
                                @foreach ($groupedStatuses as $statusName => $orders)
                                    <h1 class="text-lg text-black font-bold uppercase mb-3
                                        {{
                                            match ($statusName) {
                                                'pending' => 'text-orange-600',
                                                'completed' => 'text-blue-600',
                                                'partial-delivery' => 'text-purple-700',
                                                default => 'text-black'
                                            }
                                        }}
                                    "> 
                                        {{ $statusName }} Orders:
                                    </h1>
                                    <table class="w-full mb-5">
                                        <thead>
                                            <tr>
                                                <th>Generic Name</th>
                                                <th>Brand Name</th>
                                                <th>Form</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>QR Code</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                {{-- @foreach ($orders as $order) --}}
                                                    @php
                                                        $order_calc = $order->exclusive_deal->price * $order->quantity;
                                                        $total += $order_calc;
                                                    @endphp
                                                    <tr class="text-center">
                                                        <td>{{ $order->exclusive_deal->product->generic_name }}</td>
                                                        <td>{{ $order->exclusive_deal->product->brand_name }}</td>
                                                        <td>{{ $order->exclusive_deal->product->form }}</td>
                                                        <td>{{ $order->quantity }}</td>
                                                        <td>₱ {{ number_format($order_calc) }}</td>
                                                        <td>
                                                            <!-- Link to generate the QR code for this single order -->
                                                            <a href="{{ route('orders.showQrCode', $order->id) }}"
                                                            class="btn btn-primary">
                                                                Generate QR Code
                                                            </a>
                                                        </td>
                                                    </tr>
                                                {{-- @endforeach --}}
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endforeach
                            </div>

                            
                            {{-- Print Buttons etc. (optional) --}}
                            <div class="print-button flex flex-col sm:flex-row justify-end mt-24 gap-4 items-center">
                                <p class="text-right text-[18px] sm:text-[20px] font-bold">
                                    Grand Total: ₱ {{ number_format($total) }}
                                </p>
                                <button class="flex items-center gap-2 cursor-pointer text-sm sm:text-base">
                                    <i class="fa-solid fa-qrcode"></i>Qr Code
                                </button>
                                {{-- Example: if the last order in the group is "completed" --}}
                                {{-- @if ($groupedOrdersByCompanyName->last()->status === "completed")
                                    <button class="flex items-center gap-2 cursor-pointer text-sm sm:text-base">
                                        <i class="fa-solid fa-print"></i>Invoice
                                    </button>
                                @endif --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        @endforeach
        {{-- View Order Modal --}}

        {{-- Add New Order Modal --}}
        <div class="add-new-order-modal hidden fixed w-full h-full top-0 left-0 p-5 bg-black/50 pt-[50px]">
            <div class="modal bg-white w-full md:w-[30%] mx-auto p-5 rounded-lg relative shadow-lg">
                <x-modalclose id="addneworderclose" click="closeaddneworder"/>
                <h1 class="text-[18px] text-[#005382] font-bold">Add New Order</h1>

                <form action="" id="add-new-order" class="overflow-y-auto max-h-[400px] flex flex-col mt-5">
                    <div class="flex flex-col gap-2 items-center px-5 pb-10" id="order-form-input">
                        <x-label-input label="Customer Name:" name="customer_name" type="text" for="customer_name" divclass="flex flex-col" placeholder="Enter Customer Name"/>
                        <x-label-input label="Product Name:" name="product" type="text" for="product" divclass="flex flex-col" placeholder="Enter Product Name"/>
                        <x-label-input label="Quantity:" name="quantity" type="text" for="quantity" divclass="flex flex-col" placeholder="Enter Quantity"/>
                        <x-label-input label="Price:" name="price" type="text" for="price" divclass="flex flex-col" placeholder="Enter Price"/>
                    </div>

                    <div class="flex justify-between absolute bottom-0 w-full p-2 bg-white left-0">
                        <button id="addnewworder-button" class="bg-white flex items-center">
                            <i class="fa-solid fa-plus"></i>Add More
                        </button>
                        <button type="submit" class="bg-white p-2 rounded-lg flex items-center">
                            <img src="{{ asset('image/image 51.png') }}" class="w-[20px]">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
        {{-- Add New Order Modal --}} 
        
        {{-- Upload qr code modal --}}
        <div class="upload-qr-modal hidden fixed w-full h-full top-0 left-0 p-5 bg-black/50 pt-[50px]">
            <div class="modal bg-white w-full md:w-[30%] mx-auto p-5 rounded-lg relative shadow-lg">
                <x-modalclose id="uploadqrmodalclose" click="closeuploadqrmodal"/>
                <!-- Title -->
                <h1 class="text-xl font-semibold text-gray-800 mb-4">Upload QR Code</h1>

                <!-- Upload Form -->
                <form id="uploadForm" enctype="multipart/form-data" class="flex flex-col space-y-4">
                    <input type="file" name="qr_code" id="qr_code" accept="image/*" class="border border-gray-300 rounded-lg px-4 py-2 w-full text-gray-700 focus:ring focus:ring-blue-200 focus:outline-none" required>
                    <button type="submit" class="flex items-center justify-center space-x-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fa-solid fa-upload"></i>
                        <span>Upload</span>
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
<script src="{{ asset('js/order.js') }}"></script>
</html>
