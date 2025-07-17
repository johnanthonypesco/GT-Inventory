@props([
    'image' => 'ceftriaxone.png',
    'genericname' => '',
    'brandname' => '',
    'form' => '',
])

<div class="bg-white rounded-lg flex flex-col items-center justify-center p-5 mt-5 !w-60 product-card"
     style="box-shadow: 0 0 5px black;"
     data-type="{{ strtolower($form) }}">
    <img src="{{ asset($image) }}" alt="{{$genericname}}" class="w-[120px] h-[120px]">
    <h1 class="mt-5 font-bold text-xl text-[#084876]">{{$genericname}}</h1>
    <p class="font-semibold text-lg text-[#084876]">{{$brandname}}</p>
    <p class="font-semibold">{{$form}}</p>
</div>
