@props(['route' => '', 'routeid' => '', 'method' => 'GET'])

@if ($routeid)
    <form action="{{ route($route, $routeid) }}" method="{{in_array(strtoupper($method), ['GET', 'POST']) ? strtoupper($method) : 'POST'}}" {{$attributes}}>
        @csrf
        @method(strtoupper($method))
        <button type="submit" class="m-auto text-red-500 cursor-pointer transform duration-300 flex gap-2 items-center" {{ $attributes }}><i class="fa-solid fa-trash"></i>
            Delete
        </button>
    </form>
@else
    <form action="{{ route($route) }}" method="{{in_array(strtoupper($method), ['GET', 'POST']) ? strtoupper($method) : 'POST'}}" {{$attributes}}>
        @csrf
        @method(strtoupper($method))
        <button type="submit" class="m-auto text-red-500 cursor-pointer transform duration-300 flex gap-2 items-center" {{ $attributes }}><i class="fa-solid fa-trash"></i>
            Delete
        </button>
    </form>
@endif

