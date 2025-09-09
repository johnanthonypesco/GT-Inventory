@props([
  'id' => '', 
  'click' => '', 
  'class' => '', 
  'closeType' => 'none', 
  'variable' => null,
])

@switch($closeType)
    @case("customer-deals")
      <span id="{{$id}}" class="text-xl hover:text-lg font-semibold transition-all duration-150 text-black/80 hover:bg-gray-200/70 w-12 h-12 flex justify-center items-center rounded-full cursor-pointer absolute right-2 top-2 shadow-md shadow-black/50 hover:shadow-none  {{$class}}" 
        onclick="{{$click}}('{{ $variable }}')"
      >
        <i class="fa-regular fa-xmark"></i>
      </span>
  
      @break
    
    @case("orders-admin-view")
      <span id="{{$id}}" class="text-xl hover:text-lg font-semibold transition-all duration-150 text-black/80 hover:bg-gray-200/70 w-12 h-12 flex justify-center items-center rounded-full cursor-pointer absolute right-2 top-2 shadow-md shadow-black/50 hover:shadow-none  {{$class}}" onclick="{{$click}}('{{$variable}}')">
        <i class="fa-regular fa-xmark"></i>
      </span>
      @break

    @case("edit-product-deal")
      <span id="{{$id}}" class="text-xl hover:text-lg font-semibold transition-all duration-150 text-black/80 hover:bg-gray-200/70 w-12 h-12 flex justify-center items-center rounded-full cursor-pointer absolute right-2 top-2 shadow-md shadow-black/50 hover:shadow-none  {{$class}}" onclick="closeEditProductListing('{{$variable}}')">
        <i class="fa-regular fa-xmark"></i>
      </span>
      @break
    
      @case("order-history")
      <span id="{{$id}}" class="text-xl hover:text-lg font-semibold transition-all duration-150 text-black/80 hover:bg-gray-200/70 w-12 h-12 flex justify-center items-center rounded-full cursor-pointer absolute right-2 top-2 shadow-md shadow-black/50 hover:shadow-none  {{$class}}" onclick="closeOrderModal('{{$variable}}')">
        <i class="fa-regular fa-xmark"></i> 
      </span>
      @break

    @default
      <span id="{{$id}}" class="text-xl hover:text-lg font-semibold transition-all duration-150 text-black/80 hover:bg-gray-200/70 w-12 h-12 flex justify-center items-center rounded-full cursor-pointer absolute right-2 top-2 shadow-md shadow-black/50 hover:shadow-none  {{$class}}" onclick="{{$click}}()">
        <i class="fa-regular fa-xmark"></i>
      </span>
@endswitch

