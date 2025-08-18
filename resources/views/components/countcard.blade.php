@props(['count' => 0, 'title'=> 'Total Stocks', 'image' => 'image.png', 'classname' => ''])

<div {{ $attributes ->merge(['class' => 'bg-white w-full p-5 rounded-xl relative']) }}  id="card" style="box-shadow: 0 5px 8px rgba(0, 0, 0, 0.389)">
    <div class="flex items-center justify-between">
        <p class="text-3xl" id="real-timer-counters" data-type="{{ $title }}">0{{$count}}</p>
        <img src="{{asset ('image/'. $image)}}" alt="">
    </div>
    <p class="text-md font-semibold">{{$title}}</p>
    
    @if(trim($slot))
        <div class="{{ $classname }}">
            {{ $slot }}
        </div>
    @endif
</div>