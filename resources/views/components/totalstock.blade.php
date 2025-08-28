@props([
    'count' => 0, 
    'title'=> 'Total Stocks', 
    'image' => 'image.png', 
    'buttonType' => 'in-stock',
    'classmate' => ''
])

<div onclick="showStockModals('{{$buttonType}}')" id="card" class="cursor-pointer bg-white w-full p-5 rounded-xl relative" style="box-shadow: 0 5px 8px rgba(0, 0, 0, 0.389)">
    <div class="flex items-center justify-between">
        <p class="text-sm font-semibold text-gray-800">{{$title}}</p>
        <img src="{{asset ('image/'. $image)}}" alt="" class="{{$classmate}}">
    </div>
    <p class="text-2xl font-semibold" id="real-timer-stock-count" data-type="{{ $buttonType }}">0{{$count}}</p>
    {{-- <i class="fa-solid fa-hand-pointer text-white bg-[#005382] rounded-full p-2 absolute right-5 text-lg animate-bounce opacity-70"></i> --}}
    <div class="bg-[#005382] rounded-full p-2 absolute right-5  animate-bounce opacity-70">
        <i class="fa-solid fa-hand-pointer text-white text-lg"></i>
    </div>
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