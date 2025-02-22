@props(['route' => '', 'routeid' => '', 'method' => 'POST'])

@if ($routeid)
    <form action="{{ route($route, $routeid) }}" method="POST" {{ $attributes }}>
        @csrf
        @if (strtoupper($method) !== 'POST')
            @method(strtoupper($method))
        @endif
        <button type="button" onclick="deletesweetalert(this)" class="m-auto text-red-500 cursor-pointer transform duration-300 flex gap-2 items-center" {{ $attributes }}>
            <i class="fa-solid fa-trash"></i> Delete
        </button>
    </form>
@else
    <form action="{{ route($route) }}" method="POST" {{ $attributes }}>
        @csrf
        @if (strtoupper($method) !== 'POST')
            @method(strtoupper($method))
        @endif
        <button type="button" onclick="deletesweetalert(this)" class="m-auto text-red-500 cursor-pointer transform duration-300 flex gap-2 items-center" {{ $attributes }}>
            <i class="fa-solid fa-trash"></i> Delete
        </button>
    </form>
@endif

<script src="{{ asset('js/sweetalert/deletebuttonsweetalert.js') }}"></script>
