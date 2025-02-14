@props(['action'=>'#', 'method' => 'POST'])

<form action="{{ $action }}" method="{{in_array(strtoupper($method), ['GET', 'POST']) ? strtoupper($method) : 'POST'}}" {{$attributes}}>
    @csrf
    @if (!in_array(strtoupper($method), ['GET', 'POST']))
        @method(strtoupper($method))
    @endif
    <button type="submit" class="m-auto text-red-500 cursor-pointer transform duration-300 flex gap-2 items-center" {{ $attributes }}>
        <i class="fa-solid fa-trash"></i> Delete
    </button>
</form>
