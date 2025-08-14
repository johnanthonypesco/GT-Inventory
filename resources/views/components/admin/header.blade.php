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

<header class="flex justify-between items-center w-full md:py-2 py-4 px-5">
    <div class="flex items-center gap-2">
        <i {{ $attributes->merge(['class'=> 'text-[#005382] text-2xl '. $icon]) }}></i>
        <h1 class="font-bold text-lg uppercase">{{$title}}</h1>
    </div>

    <div class="flex items-center gap-4">
        <div class="relative group">
            <button class="flex gap-2 justify-center items-center font-semibold text-md rounded-lg bg-green-600/80 text-white w-fit p-2 hover:bg-green-600 transition-all duration-150"
                onclick="showTutorial()"
            >
                <i class="fa-regular fa-circle-question text-xl"></i>
                HELP
            </button>

            <!-- Tooltip -->
            <span class="absolute -left-4 -bottom-28 mt-2 px-3 py-1 text-base text-white bg-gray-800 rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-150 pointer-events-none animate-bounce z-50 w-[140px] text-center">
                Will show a user manual video for this page.
            </span>
        </div>

        {{-- Ensure user info container is visible in all breakpoints where needed --}}
        <div class="hidden lg:flex gap-2 items-center px-5 py-1 border border-[#005382] rounded-md">
            <i class="fa-solid fa-user text-2xl bg-[#005382] text-white p-2 rounded-full"></i>
            <div>
                <p class="font-semibold text-sm">{{ $name }}</p>
                <p class="text-[12px]">{{ $email }}</p>
            </div>
        </div>

        <x-admin.burgermenu/>
    </div>

    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 z-50 p-4 sm:p-6 md:p-10 lg:p-20 overflow-auto" id="tutorialModal">
        <div class="modal w-full max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-6 pb-11 sm:p-8 relative">
            <x-modalclose click="showTutorial" />

            <div class="w-full h-full overflow-scroll flex-col justify-center items-center">
                <h1 class="text-2xl uppercase font-semibold text-[#005382] text-center mb-3">
                        Wala pa akong ginagawa dito
                    </h1>
                <img src="{{ asset("image/yahoo-baby.png") }}" alt="byahoo" class="w-[100%] h-[100%] object-fill">
            </div>
        </div>
    </div>

    <script>
        function showTutorial() {
            const tutorialModal = document.getElementById("tutorialModal");

            if (tutorialModal.classList.contains("hidden")) {
                tutorialModal.classList.replace("hidden", 'flex');
            } else {
                tutorialModal.classList.replace('flex', "hidden");
            }
        }
    </script>
</header>

