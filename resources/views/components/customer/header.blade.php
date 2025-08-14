@props(['title' => '', 'icon' => ''])

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
<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
<header class="flex justify-between items-center w-full py-2 px-5 bg-white rounded-lg">
<div class="flex items-center gap-2">
    <i {{ $attributes->merge(['class'=> 'text-[#005382] text-2xl '. $icon]) }}></i>
    <h1 class="font-bold text-2xl uppercase">{{$title}}</h1>
</div>

<div class="flex items-center gap-4">
    {{-- <h1>unread messages: 3</h1> --}}
    {{-- Ensure user info container is visible in all breakpoints where needed --}}
    @if ($totalUnreadMessages > 0)
    <a id="fulltank" href="{{ route('customer.chat.index') }}" class="inline-block">
        <span class="bg-red-500 text-white text-xs font-semibold rounded-full px-2 py-1 flex items-center justify-center">
            <box-icon name='envelope' color="white" class="mr-1"></box-icon> <!-- BoxIcon envelope icon -->
            {{ $totalUnreadMessages }}
        </span>
    </a>
@endif

    <div class="relative group inline-block">
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

    <div class="hidden lg:flex gap-2 items-center px-5 py-1 border border-[#005382] rounded-lg">
        
        @if (Auth::user()->company && Auth::user()->company->profile_image)
            <img 
                id="profilePreviewone"
                src="{{ asset(Auth::user()->company->profile_image) }}"  
                class="w-12 h-12 object-cover border-4 border-[#005382] rounded-full bg-white p-1 shadow-md"
                alt="Company Profile Picture"
            >
        @else
            <i 
                class="fas fa-user w-12 h-12 flex items-center justify-center border-4 border-[#005382] rounded-full bg-white p-1 shadow-md" 
            ></i>
        @endif
        {{-- niremove ko kasi may lumilitaw na form input type file  --}}
        {{-- <label for="profile_image">
        </label>         --}}
        <div>
            <div class="flex items-center gap-2">
                <p class="text-sm uppercase">{{ $name }}</p>
                <p class="font-md font-semibold">{{ $company }}</p>
            </div>
            <p class="text-sm">{{ $email }}</p>
            
        </div>
    </div>
</div>
<div class="lg:hidden">
        <i class="fa-solid fa-bars text-2xl hover:cursor-pointer" onclick="openSidebar()"></i>
</div>
</header>

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


<x-customer.sidebar/>

<script>
    // function sidebar() {
    //     var sidebar = document.querySelector('#sidebar');
    //     sidebar.classList.toggle('left-0');
    //     sidebar.classList.toggle('w-[300px]');
    // }

    // function closeSidebar() {
    //     var sidebar = document.querySelector('#sidebar');
    //     sidebar.classList.remove('left-0', 'w-[300px]');
    // }
    // window.addEventListener('scroll', () => {
    //     const sidebar = document.querySelector('#sidebar');
    //     sidebar.classList.remove('left-0', 'w-[300px]');
    // });
    // add auto reload for realtime
    document.addEventListener('DOMContentLoaded', function() {
    let contactsRefreshInterval;

    // Start contacts refresh interval
    function startContactsRefresh() {
        contactsRefreshInterval = setInterval(refreshContacts, 5000); // Refresh every 6 seconds
    }

    // Stop contacts refresh interval
    function stopContactsRefresh() {
        clearInterval(contactsRefreshInterval);
    }

    // Function to refresh contacts list
    function refreshContacts() {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContactsList = doc.getElementById('fulltank');
                if (newContactsList) {
                    const currentContactsList = document.getElementById('fulltank');
                    currentContactsList.innerHTML = newContactsList.innerHTML;
                }
            })
            .catch(error => console.error('Error refreshing contacts:', error));
    }

    // Start the refresh interval when the page loads
    startContactsRefresh();
});
</script>
