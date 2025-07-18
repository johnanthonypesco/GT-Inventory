@props([
    'placeholder' => '' , 
    'classname' => '', 
    'name' => '', 
    'type' => '', 
    'divclass' => '',
    'id' => '',
    
    //Search Input Props
    'searchType' => 'none',
    'location_filter' => 'all',
    'dataList' => [],
    'autofill' => false,
    'currentSearch' => [],
])

<div {{ $attributes->merge(['class' => $divclass]) }}>
    <form action="{{ route('admin.inventory.search', ['type' => $searchType]) }}" method="post" id="search-form-{{$id}}">
        @csrf

        @if ($searchType === 'stock')
            <datalist id="search-options-{{$id}}-{{$location_filter}}">
                @foreach ($dataList as $data)
                    @php
                        $generic_name = $data->generic_name ? $data->generic_name : 'No Generic Name';
                        $brand_name = $data->brand_name ? $data->brand_name : 'No Brand Name';
                    @endphp
        
                    <option value="{{ $generic_name }} - {{ $brand_name }}"></option>            
                @endforeach
            </datalist>
        @else
            <datalist id="search-options-product">
                @foreach ($dataList as $data)
                    @php
                        $generic_name = $data->generic_name ? $data->generic_name : 'No Generic Name';
                        $brand_name = $data->brand_name ? $data->brand_name : 'No Brand Name';
                    @endphp
        
                    <option value="{{ $generic_name }} - {{ $brand_name }}"></option>            
                @endforeach
            </datalist>
        @endif

    
        <input type="hidden" required name="location_filter" value="{{ $location_filter }}">

        <input type="search" name="{{ $name }}" 
        placeholder="{{ $placeholder }}" 
        id="{{ $id }}" 
        class="w-full p-2 border border-[#005382] rounded-lg outline-[#005382]"
        autocomplete="{{$autofill ? 'on' : 'off'}}"

        @if ($searchType === 'stock')
            list="search-options-{{$id}}-{{$location_filter}}"
        @else
            list="search-options-product"            
        @endif
        value="{{ $currentSearch ? $currentSearch[0] . " - " . $currentSearch[1] : '' }}"
        onkeydown="if(event.key === 'Enter') {event.preventDefault()}"
        >
        @if ($classname)
            <button class="absolute bg-white right-1 top-2 border-l-1 border-[#005382] px-3 cursor-pointer text-xl"
            type="button"
            @if ($searchType === 'stock')
                onclick="is_in_suggestion('{{ $id }}', 'search-options-{{ $id }}-{{$location_filter}}')"                
            @else
                onclick="is_in_suggestion('{{ $id }}', 'search-options-product')"      
            @endif
            >
                <i {{$attributes->merge(['class' => $classname]) }}></i>
            </button>
        @endif
    </form>
</div>