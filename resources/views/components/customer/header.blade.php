@props(['title'=> '', 'icon' => 'fa-solid'])

@php
// Check which guard is logged in
if(auth('web')->check()) {
    $user = auth('web')->user();
    $name = $user->name;
    $email = $user->email;
    $company = $user->company->name;

} else {
    $name = 'Guest';
    $email = 'guest@example.com';
}
@endphp
<header class="flex justify-between items-center w-full py-2 px-5 bg-white rounded-lg">
<div class="flex items-center gap-2">
    <i {{ $attributes->merge(['class'=> 'text-[#005382] text-2xl '. $icon]) }}></i>
    <h1 class="font-bold text-lg uppercase">{{$title}}</h1>
</div>

<div class="flex items-center gap-4">
    {{-- <h1>unread messages: 3</h1> --}}
    {{-- Ensure user info container is visible in all breakpoints where needed --}}
    <div class="hidden md:flex gap-2 items-center px-5 py-1 border border-[#005382] rounded-lg">
        <i class="fa-solid fa-user text-2xl bg-[#005382] text-white p-2 rounded-full"></i>
        <div>
            <div class="flex items-center gap-2">
                <p class="font-semibold text-sm">{{ $company }}</p>
                <p class="text-[12px] uppercase">{{ $name}}</p>
            </div>
            <p class="text-[12px]">{{ $email }}</p>
        </div>
    </div>
</div>
<div class="lg:hidden">
    <i class="fa-solid fa-bars text-2xl hover:cursor-pointer" onclick="sidebar()"></i>
</div>
</header>
   

<x-customer.sidebar/>

<script>
    function sidebar() {
        var sidebar = document.querySelector('#sidebar');
        sidebar.classList.toggle('left-0');
        sidebar.classList.toggle('w-[300px]');
    }
</script>
