@props([
  'id' => '', 
  'click' => '', 
  'class' => '', 
  'closeType' => 'none', 
  'variable' => null,
])

@switch($closeType)
    @case("customer-deals")
      <span id="{{$id}}" class="text-xl font-semibold transition-all duration-300 text-black/80 hover:bg-gray-200/70 p-1 rounded-full cursor-pointer absolute right-2 top-2 {{$class}}" 
        onclick="{{$click}}('{{ $variable }}')"
      >
        <i class="fa-regular fa-xmark"></i>
      </span>
  
      @break
    
    @case("orders-admin-view")
      <span id="{{$id}}" class="text-xl font-semibold transition-all duration-300 text-black/80 hover:bg-gray-200/70 p-1 rounded-full cursor-pointer absolute right-2 top-2 {{$class}}" onclick="{{$click}}('{{$variable}}')">
        <i class="fa-regular fa-xmark"></i>
      </span>
      @break

    @case("edit-product-deal")
      <span id="{{$id}}" class="text-xl font-semibold transition-all duration-300 text-black/80 hover:bg-gray-200/70 p-1 rounded-full cursor-pointer absolute right-2 top-2 {{$class}}" onclick="closeEditProductListing('{{$variable}}')">
        <i class="fa-regular fa-xmark"></i>
      </span>
      @break
    
      @case("order-history")
      <span id="{{$id}}" class="text-xl font-semibold transition-all duration-300 text-black/80 hover:bg-gray-200/70 p-1 rounded-full cursor-pointer absolute right-2 top-2 {{$class}}" onclick="closeOrderModal('{{$variable}}')">
        <i class="fa-regular fa-xmark"></i>
      </span>
      @break

    @default
      <span id="{{$id}}" class="text-xl font-semibold transition-all duration-300 text-black/80 hover:bg-gray-200/70 p-1 rounded-full cursor-pointer absolute right-2 top-2 {{$class}}" onclick="{{$click}}()">
        <i class="fa-regular fa-xmark"></i>
      </span>
@endswitch

