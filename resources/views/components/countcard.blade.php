@props(['count' => 0, 'title'=> 'Total Stocks', 'image' => 'image.png'])

<div {{ $attributes }}  id="card" class="shadow-lg bg-white w-full p-5 rounded-xl">
    <div class="flex items-center justify-between">
        <p class="text-3xl">0{{$count}}</p>
        <img src="{{asset ('image/'. $image)}}" alt="">
    </div>
    <p class="text-xl font-semibold">{{$title}}</p>
</div>