@props(['count' => 0, 'title'=> 'Total Stocks', 'image' => 'image.png', 'classname' => '', 'hoverTextColor' => ''])

<div {{ $attributes ->merge(['class' => 'group bg-white w-full p-5 rounded-xl relative']) }} id="card" style="box-shadow: 0 5px 8px rgba(0, 0, 0, 0.389)">
    <div class="flex items-center justify-between">
        <p class="text-md font-semibold text-black/70 group-hover:{{ $hoverTextColor }}">{{$title}}</p>
        <img src="{{asset ('image/'. $image)}}" alt="" class="w-10">
    </div>
    <p class="text-xl font-semibold group-hover:{{ $hoverTextColor }}" id="real-timer-counters" data-type="{{ $title }}">0{{$count}}</p>
    
    @if(trim($slot))
        <div class="{{ $classname }}">
            {{ $slot }}
        </div>
    @endif
</div>