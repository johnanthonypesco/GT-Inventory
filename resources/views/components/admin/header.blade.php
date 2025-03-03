@props(['title' => '', 'icon' => ''])

@php
    // Check which guard is logged in
    if(auth('superadmin')->check()) {
        $user = auth('superadmin')->user();
        $name = $user->s_admin_username;
        $email = $user->email;
    } elseif(auth('admin')->check()) {
        $user = auth('admin')->user();
        $name = $user->username;
        $email = $user->email;
    } elseif(auth('staff')->check()) {
        $user = auth('staff')->user();
        $name = $user->staff_username;
        $email = $user->email;
    } else {
        $name = 'Guest';
        $email = 'guest@example.com';
    }
@endphp

<header class="flex justify-between items-center w-full py-2 px-5">
    <div class="flex items-center gap-2">
        <i {{ $attributes->merge(['class'=> 'text-[#005382] text-2xl '. $icon]) }}></i>
        <h1 class="font-bold text-lg uppercase">{{$title}}</h1>
    </div>

    <div class="flex items-center gap-4">
        {{-- Ensure user info container is visible in all breakpoints where needed --}}
        <div class="hidden md:flex gap-2 items-center px-5 py-1 border border-[#005382] rounded-lg">
            <i class="fa-solid fa-user text-2xl bg-[#005382] text-white p-2 rounded-full"></i>
            <div>
                <p class="font-semibold text-sm">{{ $name }}</p>
                <p class="text-[12px]">{{ $email }}</p>
            </div>
        </div>

        <x-admin.burgermenu/>
    </div>
</header>
