@props([
    'count' => 0, 
    'title'=> 'Total Stocks', 
    'image' => 'image.png', 
    'buttonType' => 'in-stock',
])

<div onclick="showStockModals('{{$buttonType}}')" id="card" class="cursor-pointer shadow-lg bg-white w-full p-5 rounded-xl">
    <div class="flex items-center justify-between">
        <p class="text-lg font-semibold text-gray-800">{{$title}}</p>
        <img src="{{asset ('image/'. $image)}}" alt="" class="w-14 h-14 p-2 rounded-full bg-gray-200">
    </div>
    <p class="text-2xl font-semibold">0{{$count}}</p>
</div>

<style>
    #card:hover{
        background: #000046;  /* fallback for old browsers */
        background: -webkit-linear-gradient(to right, #1CB5E0, #6e6ee3);  /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to right, #1CB5E0, #6e6ee3); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        color: white;
        transform: scale(1.05);
        transition: all 0.3s;
    }
</style>