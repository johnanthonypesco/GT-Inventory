@props(['route' => '', 'routeid' => '', 'deleteType' => '', 'variable' => null, 'method' => 'POST'])

@switch($deleteType)
    @case("deleteDeal")
        @if ($routeid)
            <form class="delete-deal-form" action="{{ route($route, ['deal_id' => $routeid, 'company' => $variable, 'archive']) }}" method="POST" {{ $attributes }}>
                @csrf
                @if (strtoupper($method) !== 'POST')
                    @method(strtoupper($method))
                @endif
                <button type="button" class="delete-deal-btn m-auto text-red-500 cursor-pointer transform duration-300 flex gap-2 items-center text-sm" {{ $attributes }}>
                    <i class="fa-solid fa-database"></i> Archive
                </button>
            </form>
        @else
            <form class="delete-dealelse-form" action="{{ route($route) }}" method="POST" {{ $attributes }}>
                @csrf
                @if (strtoupper($method) !== 'POST')
                    @method(strtoupper($method))
                @endif
                <button type="button" class="delete-dealelse-btn m-auto text-red-500 cursor-pointer transform duration-300 flex gap-2 items-center text-sm" {{ $attributes }}>
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
            </form>
        @endif
        @break

    @case("archive")
        <form class="archive-form" action="{{ route($route, $routeid) }}" method="POST" {{ $attributes }}>
            @csrf
            @if (strtoupper($method) !== 'POST')
                @method(strtoupper($method))
            @endif
            <button type="button" class="archive-btn m-auto text-red-500 font-bold cursor-pointer transform duration-300 flex gap-2 items-center text-sm" {{ $attributes }}>
                <i class="fa-solid fa-database"></i> Archive
            </button>
        </form>
        @break

    @default
        @if ($routeid)
            <form class="delete-default-form" action="{{ route($route, $routeid) }}" method="POST" {{ $attributes }}>
                @csrf
                @if (strtoupper($method) !== 'POST')
                    @method(strtoupper($method))
                @endif
                <button type="button" class="delete-default-btn m-auto text-red-500 cursor-pointer transform duration-300 flex gap-2 items-center text-sm" {{ $attributes }}>
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
            </form>
        @else
            <form class="delete-default-else-form" action="{{ route($route) }}" method="POST" {{ $attributes }}>
                @csrf
                @if (strtoupper($method) !== 'POST')
                    @method(strtoupper($method))
                @endif
                <button type="button" class="delete-default-else-btn m-auto text-red-500 cursor-pointer transform duration-300 flex gap-2 items-center text-sm" {{ $attributes }}>
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
            </form>
        @endif
@endswitch
