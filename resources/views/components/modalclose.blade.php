@props(['id' => '', 'click' => '', 'class' => ''])

<span id="{{$id}}" class="text-6xl font-bold text-red-600 cursor-pointer absolute -right-5 -top-7 {{$class}}" onclick="{{$click}}()"><img src="{{ asset('image/close.png') }}" class="w-[50px]"></span>
