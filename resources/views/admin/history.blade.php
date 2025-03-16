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
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Orders</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full md:ml-[16%]">
        <x-admin.header title="Order History" icon="fa-solid fa-clock-rotate-left" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- Filter --}}
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

        <div class="h-[70vh] mt-5 overflow-auto">
            @foreach ($provinces as $provinceName => $companies)
            {{-- Table for Order --}}
            <h1 class="font-bold">
                <span class="text-[#005382] text-2xl font-bold mr-2">
                    Orderered In:
                    {{ $provinceName }}
                </span>
            </h1>

            <div class="table-container mt-2 bg-white p-5 rounded-lg">
                <div class="flex flex-wrap justify-between items-center">
                    {{-- Search --}}
                    <x-input name="search" placeholder="Search Employee by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>
                    {{-- Search --}}

                    {{-- Table Button --}}
                    <div class="table-button flex gap-4 mt-5 lg:mt-0">
                        <select name="company" class="rounded-lg px-4 py-2 outline-none" style="box-shadow: 0 0 5px #00528288;">
                            <option value="company">All Company</option>
                            @foreach ($companies as $companyName => $employees)
                                <option value="{{ $companyName }}">{{ $companyName }}</option>
                            @endforeach
                        </select>
                        <button><i class="fa-solid fa-download"></i>Export</button>
                    </div>
                    {{-- Table Button --}}
                </div>

                @foreach ($companies as $companyName => $employees)
                    <h1 class="text-[20px] sm:text-[20px] font-regular mt-8 font-bold">
                        <span class="text-[#005382] text-[20px] font-bold mr-2">
                            Orderd By:
                        </span>
                        {{ $companyName }}
                    </h1>

                    <div class="overflow-auto max-h-[200px] h-fit mt-5">
                        {{-- Table --}}
                        <x-table
                        :headings="['Employee Name', 'Date', 'Total Amount', 'Action']"
                        :variable="$employees"
                        category="history"
                        />
                        {{-- Table --}}
                    </div>
                @endforeach
                {{-- Pagination --}}
                <x-pagination/>
            </div>
            {{-- Table for Order --}}
        @endforeach
        </div>

        {{-- View Order Modal --}}
        @foreach ($provinces as $companies)
            @foreach ($companies as $companyName => $employees)
                @foreach ($employees as $employeeNameAndDate => $statuses)
                    @php
                        $totalPrice = 0;
                    @endphp

                    <div id="order-modal-{{ $employeeNameAndDate }}" class="order-modal hidden bg-black/60 fixed top-0 left-0 w-full h-full items-center justify-center px-4">
                        <div class="modal order-modal-content mx-auto w-full lg:w-[70%] bg-white p-5 rounded-lg relative shadow-lg">
                            <x-modalclose click="closeOrderModal('{{ $employeeNameAndDate }}')"/>
                            {{-- Name of Selected Customer --}}
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
                            {{-- Name of Selected Customer --}}


                            {{-- Order Details --}}
                            <div class="table-container h-[360px] overflow-y-auto">
                                @foreach ($statuses as $statusName => $orders)
                                    <h1 class="text-lg text-black font-bold uppercase mb-3
                                        {{
                                            match ($statusName) {
                                                'cancelled' => 'text-red-600',
                                                'delivered' => 'text-blue-600',
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                {{-- @foreach ($orders as $order) --}}
                                                    @php
                                                        $order_calc = $order->exclusive_deal->price * $order->quantity;
                                                        $totalPrice += $order_calc;
                                                    @endphp
                                                    <tr class="text-center">
                                                        <td>{{ $order->exclusive_deal->product->generic_name }}</td>
                                                        <td>{{ $order->exclusive_deal->product->brand_name }}</td>
                                                        <td>{{ $order->exclusive_deal->product->form }}</td>
                                                        <td>{{ $order->quantity }}</td>
                                                        <td>₱ {{ number_format($order_calc) }}</td>
                                                    </tr>
                                                {{-- @endforeach --}}
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endforeach
                            </div>
                            <p class="text-right text-[18px] sm:text-[20px] font-bold mt-3">Grand Total: ₱ {{ number_format($totalPrice) }}</p>
                            {{-- Order Details --}}
                        </div>
                    </div>
                @endforeach
            @endforeach
        @endforeach
        {{-- View Order Modal --}}
    </main>

</body>
</html>
<script src="{{asset('js/history.js')}}"></script>
