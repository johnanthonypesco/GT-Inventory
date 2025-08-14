@props([
    'count' => 0, 
    'title'=> 'Total Stocks', 
    'image' => 'image.png', 
    'buttonType' => 'in-stock',
])

<div onclick="showStockModals('{{$buttonType}}')" id="card" class="cursor-pointer shadow-lg bg-white w-full p-5 rounded-xl relative">
    <div class="flex items-center justify-between">
        <p class="text-sm font-semibold text-gray-800">{{$title}}</p>
        <img src="{{asset ('image/'. $image)}}" alt="" class="w-10 h-10 p-2 rounded-full bg-gray-200">
    </div>
    <p class="text-2xl font-semibold" id="real-timer-stock-count" data-type="{{ $buttonType }}">0{{$count}}</p>
    <i class="fa-solid fa-hand-pointer text-white bg-[#005382] rounded-full p-2 absolute right-5 text-lg animate-bounce"></i>
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
    #card:hover p{
        color: white;
    }
</style>