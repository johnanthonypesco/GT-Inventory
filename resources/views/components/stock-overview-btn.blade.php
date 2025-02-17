{{-- CAN SUMMON THE  IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}

@props(['buttonType' => "none"])

<button onclick="showStockModals('{{$buttonType}}')" class="bg-blue-400 text-white p-1 rounded-md whitespace-nowrap font-bold -ml-4 hover:cursor-pointer hover:bg-blue-500 transition-colors">
    @switch($buttonType)
        @case($buttonType === "in-stock")
            View In Stocks
            @break
        @case($buttonType === "low-stock")
            View Low Stocks
            @break
        @case($buttonType === "out-stock")
            View Out of Stock
            @break
        @default
            YAHOO
    @endswitch
</button>
{{-- CAN SUMMON THE  IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}
