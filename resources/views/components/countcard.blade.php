@props(['count' => 100, 'title'=> 'Total Stocks', 'image' => 'image.png', 'buttontype' => 'in-stock'])

<div class="item-container flex gap-2 md:gap-5 sm:w-[190px] w-full p-8 sm:h-auto rounded-lg bg-white relative">
    <div class="flex flex-col">
        <p class="text-md sm:text-2xl">{{$count}}</p>
        <p class="font-bold mt-2">{{$title}}</p>
        @if ($buttontype)
            <x-stock-overview-btn  buttontype="{{$buttontype}}" />
        @endif
    </div>
    <img src="{{asset ('image/'. $image)}}" class="absolute right-2 top-2">
</div>