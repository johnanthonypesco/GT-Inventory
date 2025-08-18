@props([
    'image' => 'ceftriaxone.png',
    'genericname' => '',
    'brandname' => '',
    'form' => '',
])

<div class="bg-white rounded-lg flex flex-col items-center justify-start p-2 mt-5 w-[300px] h-[320px] product-card"
     style="box-shadow: 0 0 5px black;"
     data-type="{{ strtolower($form) }}">

    <img src="{{ asset($image) }}" alt="{{ $genericname }}" class="w-[150px] h-[150px] object-cover mb-3">

    <div class="flex flex-col items-center justify-start text-center w-full h-[100px]">
        <h1 class="font-bold text-xl text-[#084876] leading-tight line-clamp-1 w-full truncate">
            {{ $genericname }}
        </h1>
        <p class="font-semibold text-[#084876] leading-tight text-sm line-clamp-1 w-full truncate">
            {{ $brandname }}
        </p>
        <p class="font-semibold text-[#084876] leading-tight text-sm line-clamp-1 w-full truncate">
            {{ $form }}
        </p>
    </div>
</div>
