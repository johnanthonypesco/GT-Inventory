@props([
    'label'=>'', 
    'for'=>'', 
    'inputclass'=>'', 
    'divclass'=>'', 
    'labelclass'=>'',
])

<div class="{{$divclass}}">
    <label for="{{$for}}" class="text-black/80 font-semibold text-md tracking-wide {{$labelclass}}">{{$label}}</label>
    <input class="w-full p-2 border border-[#005382] rounded-lg {{$inputclass}}" {{$attributes}}>
    {{$slot}}
</div>