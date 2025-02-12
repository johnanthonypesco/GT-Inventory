@props(['placeholder' => '' , 'classname' => '', 'name' => '', 'type' => '', 'divclass' => ''])

<div {{ $attributes->merge(['class' => $divclass]) }}>
    <input type="search" name="{{ $name }}" placeholder="{{ $placeholder }}" class="w-full p-2 border border-[#005382] rounded-xl outline-none">
    @if ($classname)
        <button class="absolute right-1 top-2 border-l-1 border-[#005382] px-3 cursor-pointer text-xl"><i {{$attributes->merge(['class' => $classname]) }}></i></button>
    @endif
</div>