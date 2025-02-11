<nav class="h-[93vh] hidden lg:flex flex-col p-5 w-[280px] bg-white rounded-xl">
    <div class="flex flex-col">
        <img src="{{ asset('image/Group 39.png') }}" alt="" class="w-[180px] self-center">
        <hr class="mt-2">
    </div>

    <ul class="flex flex-col gap-5 flex-1 pt-5">
        <a href="{{ route('customer.order') }}" class="text-md"><i class="fa-solid fa-gauge"></i>Dashboard</a>
        <a href="{{ route('customer.order') }}" class="text-md active"><i class="fa-solid fa-cart-shopping"></i>Make an Order</a>
        <a href="{{ route('customer.order') }}" class="text-md"><i class="fa-brands fa-rocketchat"></i>Chat</a>
        <a href="{{ route('customer.order') }}" class="text-md"><i class="fa-solid fa-list-check"></i>Manage Order</a>
        <a href="{{ route('customer.order') }}" class="text-md"><i class="fa-solid fa-gear"></i>Manage Account</a>
        <a href="{{ route('customer.order') }}" class="text-md"><i class="fa-regular fa-clock"></i>History</a>
    </ul>

    <a href="{{ route('customer.order') }}" class="text-md"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
</nav>