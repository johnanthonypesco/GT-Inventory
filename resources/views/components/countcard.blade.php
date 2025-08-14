@props(['count' => 0, 'title'=> 'Total Stocks', 'image' => 'image.png', 'classname' => ''])

<div {{ $attributes ->merge(['class' => 'shadow-lg bg-white w-full p-5 rounded-xl relative']) }}  id="card">
    <div class="flex items-center justify-between">
        <p class="text-3xl">0{{$count}}</p>
        <img src="{{asset ('image/'. $image)}}" alt="">
    </div>
    <p class="text-md font-semibold">{{$title}}</p>
    
    @if(trim($slot))
        <div class="{{ $classname }}">
            {{ $slot }}
        </div>
    @endif
</div>