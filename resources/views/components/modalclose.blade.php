@props([
  'id' => '', 
  'click' => '', 
  'class' => '', 
  'closeType' => 'none', 
  'variable' => null,
])

@switch($closeType)
    @case("customer-deals")
      <span id="{{$id}}" class="text-6xl font-bold text-red-600 cursor-pointer absolute -right-5 -top-7 {{$class}}" 
        onclick="{{$click}}('{{ $variable }}')"
      >
        <img src="{{ asset('image/close-2.png') }}" class="w-[40px]">
      </span>
  
      @break
    
    @case("orders-admin-view")
      <span id="{{$id}}" class="text-6xl font-bold text-red-600 cursor-pointer absolute -right-5 -top-7 {{$class}}" onclick="{{$click}}('{{$variable}}')">
        <img src="{{ asset('image/close-2.png') }}" class="w-[40px]">
      </span>
      @break

    @case("edit-product-deal")
      <span id="{{$id}}" class="text-6xl font-bold text-red-600 cursor-pointer absolute -right-5 -top-7 {{$class}}" onclick="closeEditProductListing('{{$variable}}')">
        <img src="{{ asset('image/close-2.png') }}" class="w-[40px]">
      </span>
      @break

    @default
      <span id="{{$id}}" class="text-6xl font-bold text-red-600 cursor-pointer absolute -right-5 -top-7 {{$class}}" onclick="{{$click}}()">
        <img src="{{ asset('image/close-2.png') }}" class="w-[40px]">
      </span>
@endswitch

