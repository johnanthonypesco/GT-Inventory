@props([
    'placeholder' => '' , 
    'classname' => '', 
    'name' => '', 
    'type' => '', 
    'divclass' => '',
    'id' => '',
    
    //Search Input Props
    'searchType' => 'none',
    'dataList' => [],
    'autofill' => false,
    'currentSearch' => [],
])

<div {{ $attributes->merge(['class' => $divclass]) }}>
    <form action="{{ route('admin.inventory.search', ['type' => $searchType]) }}" method="post" id="search-form-{{$id}}">
        @csrf

        <datalist id="search-options-{{$id}}">
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
        id="{{ $id }}" 
        class="w-full p-2 border-1 border-[#005382] rounded-xl outline-none"
        autocomplete="{{$autofill ? 'on' : 'off'}}"
        list="search-options-{{$id}}"
        value="{{ $currentSearch ? $currentSearch[0] . " - " . $currentSearch[1] : '' }}"
        >
        @if ($classname)
            <button class="absolute right-1 top-2 border-l-1 border-[#005382] px-3 cursor-pointer text-xl"
            type="button"
            onclick="is_in_suggestion('{{ $id }}', 'search-options-{{ $id }}')"
            >
                <i {{$attributes->merge(['class' => $classname]) }}></i>
            </button>
        @endif
    </form>
</div>