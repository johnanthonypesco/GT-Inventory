{{-- CAN SUMMON THE  IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}

@props(['buttonType' => "none"])

<button onclick="showStockModals('{{$buttonType}}')" class="absolute right-2 -bottom-4">
    @switch($buttonType)
        @case($buttonType === "in-stock")
                <i class="fa-solid fa-hand-pointer bg-[#005382] rounded-full text-white p-2 text-xl cursor-pointer animate-bounce"></i> 
            @break
        @case($buttonType === "low-stock")
                <i class="fa-solid fa-hand-pointer bg-[#005382] rounded-full text-white p-2 text-xl cursor-pointer animate-bounce"></i>
            @break
        @case($buttonType === "out-stock")
                <i class="fa-solid fa-hand-pointer bg-[#005382] rounded-full text-white p-2 text-xl cursor-pointer animate-bounce"></i>
            @break
    @endswitch
</button>
{{-- CAN SUMMON THE  IN-STOCK, LOW-STOCK, AND NO STOCK PRODUCTS IN A MODAL --}}
