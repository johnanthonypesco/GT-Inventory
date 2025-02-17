{{-- SHOWS ALL IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}

@props(['modalType' => "none", 'variable' => null])

<div class="-mt-96 flex flex-col items-center gap-5 fixed w-fit h-fit z-10 top-0 left-[35%] border-1 border--black bg-white p-2 transition-all" id="{{$modalType}}-modal">
    <p onclick="showStockModals('{{ $modalType }}')" class="w-fit px-4 py-2 bg-red-500 text-white rounded-sm cursor-pointer self-end">
        X
    </p>
    <h1 class="text-lg font-bold">
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

{{-- SHOWS ALL IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}
