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

{{-- <div class="bg-[#eaeaea] h-1 fixed top-0 left-[16%] right-2 z-[51]"></div> --}}
<header class="flex justify-between items-center md:py-2 py-2 px-5 fixed top-0 left-0 lg:left-[15%] right-0 z-50 bg-white shadow-md">
    <div class="flex items-center gap-2">
        <i {{ $attributes->merge(['class'=> 'text-[#005382] text-2xl '. $icon]) }}></i>
        <h1 class="font-bold text-md uppercase">{{$title}}</h1>
    </div>


    <div class="flex items-center gap-2">
        @if (auth('superadmin')->check() || auth('admin')->check() || (auth('staff')->check() && !request()->routeIs('admin.dashboard')))
            @if (!request()->routeIs('admin.chat.*'))
                <div class="relative group">
                    <button id="help" class="flex gap-2 justify-center items-center font-semibold text-xl rounded-full py-1 bg-green-600/80 text-white w-fit px-2 cursor-pointer hover:bg-green-600 transition-all duration-150"
                        onclick="showTutorial()" style="box-shadow: 0px 5px 5px rgba(0, 0, 0, 0.4)"
                    >
                        <i class="fa-regular fa-circle-question text-xl"></i>
                        HELP
                        {{-- HELP --}}
                    </button>

                    <!-- Tooltip -->
                    <span class="absolute -left-4 -bottom-28 mt-2 px-3 py-1 text-base text-white bg-gray-800 rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-150 pointer-events-none animate-bounce z-50 w-[140px] text-center">
                        Will show a tutorial video for this page.
                    </span>
                </div>
            @endif
        @endif


        {{-- Ensure user info container is visible in all breakpoints where needed --}}
        <div class="hidden group lg:flex gap-2 items-center px-5 py-1 rounded-md relative cursor-pointer hover:bg-gray-100 hover:scale-105 transition-all duration-150">
            <i class="fa-solid fa-user text-xl bg-[#005382] text-white py-2 px-3 rounded-full"></i>
            <div>
                <p class="font-semibold text-sm">{{ $name }}</p>
                <p class="text-[12px] font-semibold text-black/60">{{ $email }}</p>
            </div>
            <i class='fa-solid fa-angle-down text-black/70 absolute right-2'></i>

            {{-- dropdown menu --}}
            <div class="absolute right-0 top-full mt-1 w-32 bg-gray-800 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="px-4 py-2 text-sm text-white flex items-center justify-center gap-5 hover:bg-gray-700 rounded-md transition-colors duration-150">
                    Logout <i class="fa-solid fa-right-from-bracket"></i>
                </a>
                <form id="logout-form" action="{{ route('user.logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>

        <x-admin.burgermenu/>
    </div>

    {{-- TUTORIAL POPUP MODAL --}}
    {{-- BTW KUNG NAG TATAKA KAYO KUNG BAKIT AYAW GUMANA NG TIME SCROLLER HINDI KASI SUPPORTED
    NG php artisan serve YUNG PAG HANDLE NG GANUNG REQUEST. BAKA GUMANA SYA SA HOSTINGER? --}}
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 z-50 p-8 lg:p-20 overflow-auto" id="tutorialModal">
        <div class="modal w-full max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-6 pb-11 sm:p-8 relative h-fit">
    <div class="w-full hidden h-full bg-black/70 fixed top-0 left-0 z-50 p-8 lg:p-20 overflow-auto" id="tutorialModal">
        <div class="modal w-full max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-6 pb-11 sm:p-8 relative h-fit">
            <x-modalclose click="showTutorial" />

            <div class="w-full h-fit overflow-scroll flex-col justify-center items-center p2">
                <h1 class="text-2xl uppercase font-semibold text-[#005382] text-center mb-3">
                    Tutorial For This Page:
                </h1>

                @php
                    $routeIs = fn ($req) =>  request()->routeIs($req);
                    $routesForVids = [
                        'admin.dashboard' => 'admin-dashboard.mp4',
                        'admin.sales' => 'sales-report.mp4',
                        'admin.inventory' => 'inventory.mp4',
                        'admin.file-ocr.index' => 'inventory.mp4',
                        'admin.productlisting' => 'product-deals.mp4',
                        'admin.order' => 'admin-order.mp4',
                        'superadmin.account.index' => 'admin-manage-acc.mp4',
                        'admin.contentmanagement' => 'content-management.mp4',
                        'superadmin.reviews.index' => 'review.mp4',
                        'admin.history' => 'admin-order-history.mp4',
                        'admin.historylog' => 'history-logs.mp4',
                        'admin.stafflocation' => 'staff-location.mp4',
                    ];
                @endphp

                <video id="tutorialVideo" 
                controls
                class="w-full h-auto rounded-lg shadow-md">
                    @foreach ($routesForVids as $routeName => $fileName)
                        @if ($routeIs($routeName))
                            <source src="{{ asset('videos/' . $fileName) }}" type="video/mp4">
                        @endif
                    @endforeach
                    Your browser does not support the video tag.
                </video>

                <div class="flex justify-center w-full">
                    <button type="button" 
                    class="py-3 px-10 bg-green-600 hover:bg-green-700 cursor-pointer transition-all duration-100 text-white font-bold tracking-widest hover:-translate-y-1 hover:shadow-black/60 hover:shadow-md my-5 self-center rounded-md text-xl text-center flex items-center gap-4"                    
                    
                    onclick="playVideo(this)"
                    >
                        <i class="fa-solid fa-video"></i>
                    
                        WATCH
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- TUTORIAL POPUP MODAL --}}


    <script>
        function showTutorial() {
            const tutorialModal = document.getElementById("tutorialModal");

            if (tutorialModal.classList.contains("hidden")) {
                tutorialModal.classList.replace("hidden", 'flex');
            } else {
                tutorialModal.classList.replace('flex', "hidden");
                playVideo();
            }
        }

        function playVideo(btn) {
            const video = document.getElementById('tutorialVideo');
            
            if (!video.paused && !video.ended) {
                video.pause();

                return;
            }

            if (btn) {
                video.play();
    
                if (video.requestFullscreen) { // normal
                    video.requestFullscreen();
                } 
    
                else if (video.webkitRequestFullscreen) { // Safari
                    video.webkitRequestFullscreen();
                }
            }
        }
    </script>
</header>

