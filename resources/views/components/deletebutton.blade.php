@props(['route' => '', 'routeid' => '', 'deleteType' => '', 'variable' => null, 'method' => 'POST'])

@switch($deleteType)
    @case("deleteDeal")
        @if ($routeid)
            <form action="{{ route($route, ['deal_id' => $routeid, 'company' => $variable]) }}" 
            method="POST" {{ $attributes }}>
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
        @break
    
    @case("archive")
        <form action="{{ route($route, $routeid) }}" method="POST" {{ $attributes }}>
            @csrf
            @if (strtoupper($method) !== 'POST')
                @method(strtoupper($method))
            @endif
            <button type="button" onclick="deletesweetalert(this)" class="m-auto text-red-500 font-bold cursor-pointer transform duration-300 flex gap-2 items-center" {{ $attributes }}>
                <i class="fa-solid fa-database"></i> Archive
            </button>
        </form>

        @break

    @default
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
@endswitch


<script src="{{ asset('js/sweetalert/deletebuttonsweetalert.js') }}"></script>
