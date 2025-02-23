@props(['count' => 0, 'title'=> 'Total Stocks', 'image' => 'image.png', 'buttontype '=> 'in-stock', 'buttonType' => 'in-stock'])

<div onclick="showStockModals('{{ $buttontype }}')" class="item-container flex gap-2 md:gap-5 sm:w-[250px] w-full p-8 sm:h-auto rounded-lg bg-white relative hover:outline hover:outline-3 hover:outline-blue-600 hover:cursor-pointer transition-all duration-100">
    <div class="flex flex-col">
        <p class="text-md sm:text-2xl">{{$count}}</p>
        <p class="font-bold mt-2">{{$title}}</p>
        <x-stock-overview-btn  buttonType="{{$buttontype}}" />
    </div>
    <p class="text-xl font-semibold">{{$title}}</p>
</div>

<style>
    #card:hover{
        background: #000046;  /* fallback for old browsers */
        background: -webkit-linear-gradient(to right, #1CB5E0, #000046);  /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to right, #1CB5E0, #000046); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        color: white;
        transform: scale(1.05);
        transition: all 0.3s;
    }
</style>