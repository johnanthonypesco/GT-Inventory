@php
    use Carbon\Carbon;
@endphp

{{-- SHOWS ALL IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}

@props(['modalType' => "none", 'variable' => null])


<div id="{{$modalType}}-modal" class="w-full h-full fixed pt-10 bg-black/70 top-0 left-0 z-20 hidden sm:px-5">
    <div class="modal w-full lg:w-[50%] h-fit md:h-fit m-auto rounded-lg bg-white p-5 relative">
        <x-modalclose click="showStockModals('{{ $modalType }}')" />
        <h1 class="text-xl font-bold text-[#005382]">
            @switch($modalType)
                @case("in-stock")
                    <div class="flex justify-between items-center mr-5">
                        Products In Stock

                        <form action="{{ route('admin.inventory.export', ['exportType' => "in-summary"]) }}" method="POST">
                            @csrf

                            <input type="hidden" name="array" value="{{ json_encode($variable) }}">

                            <button type="submit" class="flex gap-2 items-center shadow-sm shadow-[#0052829e] px-4 py-1 rounded-lg text-black hover:bg-[#005282] hover:text-white font-semibold transition duration-150"><i class="fa-solid fa-download"></i>Export</button>
                        </form>
                    </div>
                    
                    @break
                @case("low-stock")
                    <div class="flex justify-between items-center mr-5">
                        Products Low on Stock
                        
                        <form action="{{ route('admin.inventory.export', ['exportType' => "low-summary"]) }}" method="POST">
                            @csrf

                            <input type="hidden" name="array" value="{{ json_encode($variable) }}">

                            <button type="submit" class="flex gap-2 items-center shadow-sm shadow-[#0052829e] px-4 py-1 rounded-lg text-black hover:bg-[#005282] hover:text-white font-semibold transition duration-150"><i class="fa-solid fa-download"></i>Export</button>
                        </form>
                    </div>
                    @break
                @case("out-stock")
                    <div class="flex justify-between items-center mr-5">
                        Products Out of Stock
                        
                        <form action="{{ route('admin.inventory.export', ['exportType' => "out-summary"]) }}" method="POST">
                            @csrf

                            <input type="hidden" name="array" value="{{ json_encode($variable) }}">

                            <button type="submit" class="flex gap-2 items-center shadow-sm shadow-[#0052829e] px-4 py-1 rounded-lg text-black hover:bg-[#005282] hover:text-white font-semibold transition duration-150"><i class="fa-solid fa-download"></i>Export</button>
                        </form>
                    </div>
                    @break
                @case("near-expiry-stock")
                    <div class="flex justify-between items-center mr-5">
                        Stocks About to Expire Next Month 
                        
                        <form action="{{ route('admin.inventory.export', ['exportType' => "near-expiry-summary"]) }}" method="get">
                            <button type="submit" class="flex gap-2 items-center shadow-sm shadow-[#0052829e] px-4 py-1 rounded-lg text-black hover:bg-[#005282] hover:text-white font-semibold transition duration-150"><i class="fa-solid fa-download"></i>Export</button>
                        </form>
                    </div>
                    @break
                @case("expired-stock")
                    <div class="flex justify-between items-center mr-5">
                        Currently Expired Stocks in Inventory
                        
                        <form action="{{ route('admin.inventory.export', ['exportType' => "expired-summary"]) }}" method="get">
                            <button type="submit" class="flex gap-2 items-center shadow-sm shadow-[#0052829e] px-4 py-1 rounded-lg text-black hover:bg-[#005282] hover:text-white font-semibold transition duration-150"><i class="fa-solid fa-download"></i>Export</button>
                        </form>
                    </div>
                    @break
                @default
                    YAHOO
            @endswitch
        </h1>
        <div id="real-timer-notifs-modals" data-type="{{ $modalType }}" class="overflow-auto h-[70vh]">
    
            @if (in_array($modalType, ['in-stock', 'low-stock', 'out-stock']))
                {{-- @php
                    $collect = collect($variable)->groupBy(function ($pairs) {
                        return $pairs['inventory']->map(function ($stocks) {
                            return $stocks->location->province;
                        });
                    });
                    dd($collect);
                @endphp --}}
    
                @foreach ($variable as $provinceName => $trioArray)
                    <h1 class="text-2xl font-bold uppercase text-blue-600 my-3">
                        Assigned In: {{ $provinceName }}
                    </h1>    
    
                    <table>
                        <thead>
                            <th>Generic Name</th>
                            <th>Brand Name</th>
                            <th>Form</th>
                            <th>Strength</th>
                            <th>Current Quantity</th>
                        </thead>
                        <tbody>
                            @foreach ($trioArray as $generalInfo)
                                <tr>
                                    @foreach ($generalInfo['inventory'] as $stock)
                                        <td> {{ $stock->product->generic_name }} </td>
                                        <td> {{ $stock->product->brand_name }} </td>
                                        <td> {{ $stock->product->form }} </td>
                                        <td> {{ $stock->product->strength }} </td>
                                        @break
                                    @endforeach
                                    <td>{{ $generalInfo['total'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
    
            @else
                @foreach ($variable as $location => $stocks)
                    <h1 class="text-lg font-bold uppercase my-3">
                        Assigned In: {{ $location }}
                    </h1>
                    <table>
                        <thead>
                            <th>Batch Number</th>
                            <th>Generic Name</th>
                            <th>Brand Name</th>
                            <th>Form</th>
                            <th>Strength</th>
                            <th>Quantity</th>
                            <th>Expiration Date</th>
                        </thead>
                        <tbody>
                            @foreach ($stocks as $stock)
                                <tr class="text-center">
                                    <td> {{ $stock->batch_number }} </td>
                                    <td>{{ $stock->product->generic_name }}</td>
                                    <td>{{ $stock->product->brand_name }}</td>
                                    <td>{{ $stock->product->form }}</td>
                                    <td>{{ $stock->product->strength }}</td>
                                    <td>{{ $stock->quantity }}</td>
                                    <td>{{ Carbon::parse($stock->expiry_date)->translatedFormat('M j, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            @endif
        </div>
    </div>
</div>

{{-- SHOWS ALL IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}
