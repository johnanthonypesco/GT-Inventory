@props(['count' => 0, 'title'=> 'Total Stocks', 'image' => 'image.png', 'buttontype '=> 'in-stock'])

<div onclick="showStockModals('{{ $buttontype }}')" class="item-container flex gap-2 md:gap-5 sm:w-[250px] w-full p-8 sm:h-auto rounded-lg bg-white relative hover:outline hover:outline-3 hover:outline-blue-600 hover:cursor-pointer transition-all duration-100">
    <div class="flex flex-col">
        <p class="text-md sm:text-2xl">{{$count}}</p>
        <p class="font-bold mt-2">{{$title}}</p>
        <x-stock-overview-btn  buttonType="{{$buttontype}}" />
    </div>
    <img src="{{asset ('image/'. $image)}}" class="absolute right-2 top-2">
</div>