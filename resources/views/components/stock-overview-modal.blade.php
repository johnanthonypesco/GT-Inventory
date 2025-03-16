@php
    use Carbon\Carbon;
@endphp

{{-- SHOWS ALL IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}

@props(['modalType' => "none", 'variable' => null])


<div id="{{$modalType}}-modal" class="w-full h-full fixed pt-10 bg-black/70 top-0 left-0 z-20 hidden">
    <div class="w-[50px] h-[50px] absolute right-[270px] top-[48px] z-50 ">
        <x-modalclose click="showStockModals('{{ $modalType }}')" />
    </div>

    <div class="modal flex flex-col gap-5 mx-auto h-[550px] overflow-y-auto z-10 overflow-x-hidden  bg-white p-5 w-full lg:w-[60%] transition-all relative rounded-lg">
        <h1 class="text-2xl font-bold text-[#005382]">
            @switch($modalType)
                @case("in-stock")
                    <div class="flex justify-between items-center mr-5">
                        Products In Stock

                        <form action="{{ route('admin.inventory.export', ['exportType' => "in-summary"]) }}" method="POST">
                            @csrf

                            <input type="hidden" name="array" value="{{ json_encode($variable) }}">

                            <button type="submit" class="px-5 py-2 rounded-md outline-4 outline-blue-600 duration-150 bg-transparent hover:bg-blue-600 hover:text-white cursor-pointer flex items-center gap-1"><i class="fa-solid fa-download"></i>Export</button>
                        </form>
                    </div>
                    
                    @break
                @case("low-stock")
                    <div class="flex justify-between items-center mr-5">
                        Products Low on Stock
                        
                        <form action="{{ route('admin.inventory.export', ['exportType' => "low-summary"]) }}" method="POST">
                            @csrf

                            <input type="hidden" name="array" value="{{ json_encode($variable) }}">

                            <button type="submit" class="px-5 py-2 rounded-md outline-4 outline-blue-600 duration-150 bg-transparent hover:bg-blue-600 hover:text-white cursor-pointer flex items-center gap-1"><i class="fa-solid fa-download"></i>Export</button>
                        </form>
                    </div>
                    @break
                @case("out-stock")
                    <div class="flex justify-between items-center mr-5">
                        Products Out of Stock
                        
                        <form action="{{ route('admin.inventory.export', ['exportType' => "out-summary"]) }}" method="POST">
                            @csrf

                            <input type="hidden" name="array" value="{{ json_encode($variable) }}">

                            <button type="submit" class="px-5 py-2 rounded-md outline-4 outline-blue-600 duration-150 bg-transparent hover:bg-blue-600 hover:text-white cursor-pointer flex items-center gap-1"><i class="fa-solid fa-download"></i>Export</button>
                        </form>
                    </div>
                    @break
                @case("near-expiry-stock")
                    <div class="flex justify-between items-center mr-5">
                        Products About to Expire Next Month 
                        
                        <form action="{{ route('admin.inventory.export', ['exportType' => "near-expiry-summary"]) }}" method="get">
                            <button type="submit" class="px-5 py-2 rounded-md outline-4 outline-blue-600 duration-150 bg-transparent hover:bg-blue-600 hover:text-white cursor-pointer flex items-center gap-1"><i class="fa-solid fa-download"></i>Export</button>
                        </form>
                    </div>
                    @break
                @case("expired-stock")
                    <div class="flex justify-between items-center mr-5">
                        Products Currently Expired in Inventory
                        
                        <form action="{{ route('admin.inventory.export', ['exportType' => "expired-summary"]) }}" method="get">
                            <button type="submit" class="px-5 py-2 rounded-md outline-4 outline-blue-600 duration-150 bg-transparent hover:bg-blue-600 hover:text-white cursor-pointer flex items-center gap-1"><i class="fa-solid fa-download"></i>Export</button>
                        </form>
                    </div>
                    @break
                @default
                    YAHOO
            @endswitch
        </h1>

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
                <h1 class="text-2xl font-bold uppercase text-blue-600">
                    Assigned In: {{ $provinceName }}
                </h1>    

                <table>
                    <thead>
                        <th>Generic Name</th>
                        <th>Brand Name</th>
                        <th>Current Quantity</th>
                    </thead>
                    <tbody>
                        @foreach ($trioArray as $generalInfo)
                            <tr>
                                @foreach ($generalInfo['inventory'] as $stock)
                                    <td> {{ $stock->product->generic_name }} </td>
                                    <td> {{ $stock->product->brand_name }} </td>
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
                <h1 class="text-lg font-bold uppercase">
                    Assigned In: {{ $location }}
                </h1>
                <table>
                    <thead>
                        <th>Batch Number</th>
                        <th>Generic Name</th>
                        <th>Brand Name</th>
                        <th>Current Quantity</th>
                        <th>Expiration Date</th>
                    </thead>
                    <tbody>
                        @foreach ($stocks as $stock)
                            <tr class="text-center">
                                <td> {{ $stock->batch_number }} </td>
                                <td>{{ $stock->product->generic_name }}</td>
                                <td>{{ $stock->product->brand_name }}</td>
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

{{-- SHOWS ALL IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}
