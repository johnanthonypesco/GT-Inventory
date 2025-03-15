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
    <h1 class="font-bold text-2xl uppercase">{{$title}}</h1>
</div>

<div class="flex items-center gap-4">
    {{-- <h1>unread messages: 3</h1> --}}
    {{-- Ensure user info container is visible in all breakpoints where needed --}}
    <div class="hidden md:flex gap-2 items-center px-5 py-1 border border-[#005382] rounded-lg">
        <label for="profile_image">
            @if (Auth::user()->company && Auth::user()->company->profile_image)
                <img 
                    id="profilePreviewone"
                    src="{{ asset('storage/' . Auth::user()->company->profile_image) }}"
                    class="w-12 h-12 object-cover border-4 border-[#005382] rounded-full bg-white p-1 shadow-md"
                    alt="Company Profile Picture"
                >
            @else
                <i 
                    class="fas fa-user w-32 h-32 flex items-center justify-center border-4 border-[#005382] rounded-full bg-white p-1 shadow-md" 
                    style="font-size: 4rem;"
                ></i>
            @endif
        </label>        <div>
            <div class="flex flex-col items-start gap-2 col-span-1">
                <p class="font-semibold text-3xl">{{ $company }}</p>
                <p class="text-[24px] uppercase">{{ $name }}</p>
            </div>
            <p class="text-[18px]">{{ $email }}</p>
            
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
