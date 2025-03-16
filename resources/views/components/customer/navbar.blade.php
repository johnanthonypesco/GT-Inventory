<nav class="h-[96vh] hidden lg:flex flex-col p-5 w-[16%] bg-white rounded-xl fixed top-4 left-4">
    <div class="flex flex-col">
        <img src="{{ asset('image/Logowname.png') }}" alt="" class="w-[180px] self-center">
        <hr class="mt-2">
    </div>

    <ul class="flex flex-col gap-5 flex-1 pt-5">
        <a href="{{ route('customer.order') }}" class="text-md"><i class="fa-solid fa-cart-shopping"></i>Make an Order</a>
        <a href="{{ route('customer.manageorder') }}" class="text-md"><i class="fa-solid fa-list-check"></i>Manage Order</a>
        <a href="{{ route('customer.history') }}" class="text-md"><i class="fa-regular fa-clock"></i>Order History</a>
        <a href="{{ route('customer.chat.index') }}" id="chatNav" class="text-md relative">
            <i class="fa-brands fa-rocketchat"></i>Chat
            @if ($totalUnreadMessages > 0)
                <span class="absolute top-2.5 right-2 bg-red-500 text-white p-1 px-2 rounded-full text-xs">
                    {{ $totalUnreadMessages }}
                </span>
            @endif
        </a>
        <a href="{{ route('customer.manageaccount') }}" class="text-md"><i class="fa-solid fa-gear"></i>Manage Account</a>
    </ul>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="flex items-center gap-2 text-md uppercase bg-[#005382] p-2 text-white rounded-lg w-full">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
    </form>
</nav>

<script src="{{ asset('js/customer/navbar.js') }}"></script>