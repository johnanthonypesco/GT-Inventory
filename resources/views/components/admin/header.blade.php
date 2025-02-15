@props(['title' => '', 'icon' => '', 'name' => '', 'gmail' => ''])

<header class="flex justify-between py-2 px-5 items-center">
    <div>
        <h1 class="font-bold text-lg flex gap-2 items-center uppercase"><i {{$attributes->merge(['class'=> 'text-[#005382] text-2xl '. $icon])}}></i>{{$title}}</h1>
    </div>

    <div class="flex gap-4 items-center hidden md:flex">
        {{-- <button class="flex items-center"><i class="fa-solid fa-bell text-xl text-[#005382]"></i></button> --}}
        <div class="user-container flex gap-2 items-center px-5 py-1 rounded-[20px]">
            <i class="fa-solid fa-user text-2xl bg-[#005382] text-white p-2 rounded-full"></i>
            <div>
                <p class="font-semibold text-sm">{{$name}}</p>
                <p class="text-[12px]">{{$gmail}}</p>
            </div>
        </div>
    </div>
    <x-admin.burgermenu/>
</header>