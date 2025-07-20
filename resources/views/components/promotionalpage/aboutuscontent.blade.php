@props([
    'description' => '',
])

<div class="flex gap-2">
    <img src="{{ asset('image/icon.png') }}" alt="Check" class="w-8 h-8">
    <p>{{$description}}
    </p>
</div>