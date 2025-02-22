@props([
    'placeholder' => '' , 
    'classname' => '', 
    'name' => '', 
    'type' => '', 
    'divclass' => '',
    
    //Search Input Props
    'dataList' => [],
    'autofill' => false,
    'currentSearch' => [],
])

<div {{ $attributes->merge(['class' => $divclass]) }}>
    <form action="{{ route('admin.inventory.search') }}" method="post" id="search-form">
        @csrf

        <datalist id="search_options">
            @foreach ($dataList as $data)
                @php
                    $generic_name = $data->generic_name ? $data->generic_name : 'No Generic Name';
                    $brand_name = $data->brand_name ? $data->brand_name : 'No Brand Name';
                @endphp
    
                <option value="{{ $generic_name }} - {{ $brand_name }}"></option>            
            @endforeach
        </datalist>
    
        <input type="search" name="{{ $name }}" 
        placeholder="{{ $placeholder }}" 
        id="search" 
        class="w-full p-2 border-1 border-[#005382] rounded-xl outline-none"
        autocomplete="{{$autofill ? 'on' : 'off'}}"
        list="search_options"
        value="{{ $currentSearch ? $currentSearch[0] . " - " . $currentSearch[1] : '' }}"
        >
        @if ($classname)
            <button class="absolute right-1 top-2 border-l-1 border-[#005382] px-3 cursor-pointer text-xl"
            type="button"
            onclick="is_in_suggestion(event)"
            >
                <i {{$attributes->merge(['class' => $classname]) }}></i>
            </button>
        @endif
    </form>
</div>