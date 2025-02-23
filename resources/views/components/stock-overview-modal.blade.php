{{-- SHOWS ALL IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}

@props(['modalType' => "none", 'variable' => null])

<div id="{{$modalType}}-modal" class="w-full h-full fixed pt-20 bg-black/70 top-0 left-0 z-20 hidden">
    <div class="modal flex flex-col gap-5 mx-auto h-fit z-10  bg-white p-5 w-full lg:w-[60%] transition-all relative rounded-lg">
        <span onclick="showStockModals('{{ $modalType }}')" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer bg-white rounded-xl px-4 border-4 border-red-400 hover:text-white hover:bg-red-500 hover:border-black transition-all duration-[0.25s]">&times;</span>
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
                <th>Generic Name</th>
                <th>Brand Name</th>
                <th>Current Quantity</th>
            </thead>
            <tbody>
                @foreach ($variable as $generic_product)
                    <tr>
                        @foreach ($generic_product['inventory'] as $product)
                            <td> {{ $product->product->generic_name }} </td>
                            <td> {{ $product->product->brand_name }} </td>
                            @break
                        @endforeach
                        <td>{{ $generic_product['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- SHOWS ALL IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}
