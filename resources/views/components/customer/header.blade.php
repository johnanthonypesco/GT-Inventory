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
    // add auto reload for realtime
document.addEventListener('DOMContentLoaded', function() {
    let contactsRefreshInterval;

    // Start contacts refresh interval
    function startContactsRefresh() {
        contactsRefreshInterval = setInterval(refreshContacts, 4000); // Refresh every 6 seconds
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
