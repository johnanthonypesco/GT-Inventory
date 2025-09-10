@props([
  'id' => '', 
  'click' => '', 
  'class' => '', 
  'closeType' => 'none', 
  'variable' => null,
])
<style>
    .closemodal:hover i {
      animation: rotate 2.5s infinite;
    }
    .closemodal:hover{
      box-shadow: 0 0 6px rgba(6, 6, 6, 0.635)2;
    }
    .closemodal i {
      transition: all 0.9s;

    }
    @keyframes rotate {
      20% {
        transform: rotate(30deg);
=        scale: 0.9;
      }
      80% {
        transform: rotate(-360deg);
        scale: 1.3;
      }
      20% {
        transform: rotate(0deg);
        scale: 0.9;
      }
    }
</style>

@switch($closeType)
    @case("customer-deals")
      <span id="{{$id}}" class="text-xl font-semibold transition-all duration-150 text-black/80 hover:bg-gray-300 w-10 h-10 flex justify-center items-center rounded-full cursor-pointer absolute right-2 top-2 bg-gray-200 closemodal  {{$class}}" 
        onclick="{{$click}}('{{ $variable }}')"
      >
        <i class="fa-regular fa-xmark"></i>
      </span>
  
      @break
    
    @case("orders-admin-view")
      <span id="{{$id}}" class="text-xl font-semibold transition-all duration-150 text-black/80 hover:bg-gray-300 w-10 h-10 flex justify-center items-center rounded-full cursor-pointer absolute right-2 top-2 bg-gray-200 closemodal  {{$class}}" onclick="{{$click}}('{{$variable}}')">
        <i class="fa-regular fa-xmark"></i>
      </span>
      @break

    @case("edit-product-deal")
      <span id="{{$id}}" class="text-xl font-semibold transition-all duration-150 text-black/80 hover:bg-gray-300 w-10 h-10 flex justify-center items-center rounded-full cursor-pointer absolute right-2 top-2 bg-gray-200 closemodal  {{$class}}" onclick="closeEditProductListing('{{$variable}}')">
        <i class="fa-regular fa-xmark"></i>
      </span>
      @break
    
      @case("order-history")
      <span id="{{$id}}" class="text-xl font-semibold transition-all duration-150 text-black/80 hover:bg-gray-300 w-10 h-10 flex justify-center items-center rounded-full cursor-pointer absolute right-2 top-2 bg-gray-200 closemodal  {{$class}}" onclick="closeOrderModal('{{$variable}}')">
        <i class="fa-regular fa-xmark"></i> 
      </span>
      @break

    @default
      <span id="{{$id}}" class="text-xl font-semibold transition-all duration-150 text-black/80 hover:bg-gray-300 w-10 h-10 flex justify-center items-center rounded-full cursor-pointer absolute right-2 top-2 bg-gray-200 closemodal  {{$class}}" onclick="{{$click}}()">
        <i class="fa-regular fa-xmark"></i>
      </span>
@endswitch

