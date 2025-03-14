@props([
    'label'=>'', 
    'for'=>'', 
    'inputclass'=>'', 
    'divclass'=>'', 
    'labelclass'=>'',
    'errorChecker' => null,
    'id'=> '',
    'inputid' => ''
])

<div class="{{$divclass}}" id="{{$id}}">
    <label for="{{$for}}" class="text-black/80 font-semibold text-md tracking-wide {{$labelclass}}">
        {{$label}} 
        @if ($errorChecker !== null)
            <span class="text-red-600">
                {{ str_replace(['_', '.0'], ' ', $errorChecker) }}
            </span>
        @endif
    </label>
    <input required id="{{$inputid}}" class="w-full p-2 rounded-lg border border-[#005382] {{$inputclass}}" {{$attributes}} style="{{ $errorChecker ? 'outline: 2px solid red; border:none;' : '' }}">
    {{$slot}}
</div>