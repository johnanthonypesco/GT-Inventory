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
    <link rel="icon" href="{{ asset('image/Logowname.png') }}" type="image/png">
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <link rel="stylesheet" href="{{asset ('css/history.css')}}">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Orders</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full md:ml-[16%]">
        <x-admin.header title="Order History" icon="fa-solid fa-clock-rotate-left" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- Filter Section --}}
        <div class="mt-10 flex flex-col md:flex-row justify-between">
            <div class="flex gap-5 m-auto lg:m-0">
                <button class="text-[#005382] text-xl border-b-2 border-[#005382] font-semibold">All Orders</button>
                <button class="text-gray-500 text-xl font-semibold">Completed</button>
                <button class="text-gray-500 text-xl font-semibold">Cancelled</button>
            </div>
            <select name="location" id="location" class="border p-2 rounded-lg mt-2 text-[#005382] font-bold bg-white outline-none">
                <option value="location">All Location</option>
                <option value="location">Tarlac</option>
                <option value="location">Cabanatuan</option>
            </select>
        </div>

        {{-- Main Content Area --}}
        <div class="h-[70vh] mt-5 overflow-auto">
            @foreach ($provinces as $provinceName => $companies)
                <h1 class="font-bold">
                    <span class="text-[#005382] text-2xl font-bold mr-2">
                        Ordered In: {{ $provinceName }}
                    </span>
                </h1>
                <div class="table-container mt-2 bg-white p-5 rounded-lg">
                    <div class="flex flex-wrap justify-between items-center">
                        <x-input name="search" placeholder="Search Employee by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>
                        <div class="table-button flex gap-4 mt-5 lg:mt-0">
                            <select name="company" class="rounded-lg px-4 py-2 outline-none" style="box-shadow: 0 0 5px #00528288;">
                                <option value="company">All Company</option>
                                @foreach ($companies as $companyName => $employees)
                                    <option value="{{ $companyName }}">{{ $companyName }}</option>
                                @endforeach
                            </select>
                            <button><i class="fa-solid fa-download"></i>Export</button>
                        </div>
                    </div>

                    @foreach ($companies as $companyName => $employees)
                        <h1 class="text-[20px] sm:text-[20px] font-regular mt-8 font-bold">
                            <span class="text-[#005382] text-[20px] font-bold mr-2">Ordered By:</span>
                            {{ $companyName }}
                        </h1>
                        <div class="overflow-auto max-h-[200px] h-fit mt-5">
                            <x-table :headings="['Employee Name', 'Date', 'Total Amount', 'Action']" :variable="$employees" category="history" />
                        </div>
                    @endforeach
                    <x-pagination/>
                </div>
            @endforeach
        </div>

        {{-- View Order Modal --}}
        @foreach ($provinces as $companies)
            @foreach ($companies as $employees)
                @foreach ($employees as $employeeNameAndDate => $statuses)
                    <div id="order-modal-{{ $employeeNameAndDate }}" class="order-modal hidden bg-black/60 fixed top-0 left-0 w-full h-full items-center justify-center px-4 z-50">
                        <div class="modal order-modal-content mx-auto w-full lg:w-[70%] bg-white p-5 rounded-lg relative shadow-lg">
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

                            {{-- ✅ START: RESTRUCTURED MODAL CONTENT --}}
                            <div class="table-container h-[400px] overflow-y-auto">

                                @php
                                    // First, combine all orders (delivered, cancelled, etc.) into a single collection
                                    $allOrdersInGroup = collect($statuses)->flatten();
                                    // Now, group these items by their actual order_id. This is the key change.
                                    $groupedByOrderId = $allOrdersInGroup->groupBy('order_id');
                                @endphp

                                {{-- Loop through each unique order within this date group --}}
                                @foreach($groupedByOrderId as $orderId => $orderItems)
                                    @php
                                        // Get the first item to access common properties like status
                                        $firstItem = $orderItems->first();
                                        $statusName = $firstItem->status;
                                    @endphp

                                    {{-- Create a distinct card for each order --}}
                                    <div class="border rounded-lg p-4 mb-4 shadow-sm">
                                        <h2 class="text-lg font-bold
                                            {{ match ($statusName) {
                                                'cancelled' => 'text-red-600',
                                                'delivered' => 'text-blue-600',
                                                default => 'text-black'
                                            } }}">
                                            Order #{{ $orderId }} - <span class="uppercase">{{ $statusName }}</span>
                                        </h2>

                                        {{-- Table for items in THIS specific order --}}
                                        <table class="w-full mt-2 text-sm">
                                            <thead class="bg-gray-50">
                                                <tr class="text-left">
                                                    <th class="p-2">Generic Name</th>
                                                    <th class="p-2">Brand Name</th>
                                                    <th class="p-2">Quantity</th>
                                                    <th class="p-2">Price</th>
                                                    <th class="p-2">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($orderItems as $item)
                                                    <tr class="border-b">
                                                        <td class="p-2">{{ $item->generic_name }}</td>
                                                        <td class="p-2">{{ $item->brand_name }}</td>
                                                        <td class="p-2">{{ $item->quantity }}</td>
                                                        <td class="p-2">₱{{ number_format($item->price, 2) }}</td>
                                                        <td class="p-2">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        {{-- Display Batch Details for THIS specific order --}}
                                        @if($statusName === 'delivered' && $firstItem->scannedQrCode && $firstItem->scannedQrCode->affected_batches)
                                            <div class="mt-3 pt-3 border-t">
                                                <h4 class="text-sm font-bold text-gray-700">Batch Deduction Details:</h4>
                                                <table class="w-full text-xs text-left mt-1">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th class="p-2">Batch Number</th>
                                                            <th class="p-2">Expiry Date</th>
                                                            <th class="p-2 text-center">Qty Deducted</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                       @php
    // This is a safety check for backwards compatibility.
    $batches = $firstItem->scannedQrCode->affected_batches;
    if (is_string($batches)) {
        // If it's a string, decode it into an array.
        $batches = json_decode($batches, true);
    }
@endphp

{{-- Only loop if $batches is a valid array --}}
@if(is_array($batches))
    @foreach ($batches as $batch)
        <tr class="border-b">
            <td class="p-2">{{ $batch['batch_number'] ?? 'N/A' }}</td>
            <td class="p-2">{{ isset($batch['expiry_date']) ? Carbon::parse($batch['expiry_date'])->format('M d, Y') : 'N/A' }}</td>
            <td class="p-2 text-center">{{ $batch['deducted_quantity'] ?? 'N/A' }}</td>
        </tr>
    @endforeach
@endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            {{-- ✅ END: RESTRUCTURED MODAL CONTENT --}}
                        </div>
                    </div>
                @endforeach
            @endforeach
        @endforeach
    </main>
</body>
</html>
<script src="{{asset('js/history.js')}}"></script>
