{{-- SHOWS ALL IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}

@props(['modalType' => "none", 'variable' => null])

<div id="{{$modalType}}-modal" class="w-full h-full fixed pt-20 bg-black/70 top-0 left-0 z-20 hidden">
    <div class="modal flex flex-col gap-5 mx-auto h-fit z-10  bg-white p-5 w-full lg:w-[60%] transition-all relative rounded-lg">
        <span onclick="showStockModals('{{ $modalType }}')" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">&times;</span>
        <h1 class="text-lg font-bold text-[#005382]">
            @switch($modalType)
                @case($modalType === "in-stock")
                    Products In Stock
                    @break
                @case($modalType === "low-stock")
                    Products Low on Stock
                    @break
                @case($modalType === "out-stock")
                    Products Out of Stock
                    @break
                @default
                    YAHOO
            @endswitch
        </h1>
        <table>
            <thead>
                <tr class="bg-blue-200">
                    <th>Generic Name</th>
                    <th>Brand Name</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @if ($variable->count() > 0)
                    @foreach ($variable as $display)
                    <tr class="bg-white">
                        <td>{{ $display->product->generic_name }}</td>
                        <td>{{ $display->product->brand_name }}</td>
                        <td>{{$display->quantity}}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>    
    </div>
</div>

{{-- SHOWS ALL IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}
