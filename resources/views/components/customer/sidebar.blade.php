
<div class="flex flex-col gap-2 h-full w-0 fixed top-0 -left-20 bg-white z-20 p-5 list-none transition-all duration-500 overflow-hidden" id="sidebar">
    <li class="list-none flex-1 flex flex-col gap-5 pt-5">
        <a href="{{ route('customer.order') }}" class="text-md"><i class="fa-solid fa-cart-shopping"></i>Make an Order</a>
        <a href="{{ route('customer.manageorder') }}" class="text-md"><i class="fa-solid fa-list-check"></i>Manage Order</a>
        <a href="{{ route('customer.history') }}" class="text-md"><i class="fa-regular fa-clock"></i>Order History</a>
        <a href="{{ route('customer.chat.index') }}" id="chatSidebar" class="text-md relative">
            <i class="fa-brands fa-rocketchat"></i>Chat
            @if ($totalUnreadMessages > 0)
                <span class="absolute top-2.5 right-2 bg-red-500 text-white p-1 px-2 rounded-full text-xs">
                    {{ $totalUnreadMessages }}
                </span>
            @endif
        </a>
        <a href="{{ route('customer.manageaccount') }}" class="text-md"><i class="fa-solid fa-gear"></i>Account</a>
        {{-- <a href="{{ route('customer.chat') }}" class="text-md"><i class="fa-brands fa-rocketchat"></i>Chat</a> --}}
    <li>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-md uppercase bg-[#005382] p-2 text-white rounded-lg w-full">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </form>
</div>

<script>
    // Active Link
    var currentLocation = window.location.href;
    var navLinks = document.querySelectorAll('#sidebar a');
    navLinks.forEach(link => {
        if(link.href === currentLocation) {
            link.classList.add('active');
        }
    });

    // close when resize
    window.addEventListener('resize', () => {
        const sidebar = document.querySelector('#sidebar')
        sidebar.classList.remove('left-0')
        sidebar.classList.remove('w-[300px]')
    });

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
                const newContactsList = doc.getElementById('chatSidebar');
                if (newContactsList) {
                    const currentContactsList = document.getElementById('chatSidebar');
                    currentContactsList.innerHTML = newContactsList.innerHTML;
                }
            })
            .catch(error => console.error('Error refreshing contacts:', error));
    }

    // Start the refresh interval when the page loads
    startContactsRefresh();
});
</script>