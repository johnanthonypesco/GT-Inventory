@props(['title'=> '', 'icon' => 'fa-solid'])

<header class="w-full bg-white px-5 py-3 rounded-xl flex justify-between items-center">
    <h1 class="font-bold text-2xl flex items-center gap-2"><i {{$attributes->merge(['class'=> 'text-[#005382] ' .$icon])}}></i>{{ $title }}</h1>
    <div class="hidden lg:flex gap-2 px-3 py-1 items-center border border-[#005382] w-fit rounded-full">
            <i class="fa-regular fa-user text-white text-lg w-fit bg-[#005382] p-2 rounded-full"></i>
        <div>
            <h1 class="font-bold text-md">Wesleyan Hospital</h1>
            <p class="text-sm">wesleyan@gmail.com</p>
        </div>
    </div>
</header>