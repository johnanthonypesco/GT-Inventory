<nav class="h-[93vh] hidden lg:flex flex-col p-5 w-[280px] bg-white rounded-xl">
    <div class="flex flex-col">
        <img src="{{ asset('image/Group 39.png') }}" alt="" class="w-[180px] self-center">
        <hr class="mt-2">
    </div>

    <ul class="flex flex-col gap-5 flex-1 pt-5">
        <a href="{{ route('customer.order') }}" class="text-md"><i class="fa-solid fa-cart-shopping"></i>Make an Order</a>
        <a href="{{ route('customer.chat') }}" class="text-md"><i class="fa-brands fa-rocketchat"></i>Chat</a>
        <a href="{{ route('customer.manageorder') }}" class="text-md"><i class="fa-solid fa-list-check"></i>Manage Order</a>
        <a href="{{ route('customer.manageaccount') }}" class="text-md"><i class="fa-solid fa-gear"></i>Manage Account</a>
        <a href="{{ route('customer.history') }}" class="text-md"><i class="fa-regular fa-clock"></i>History</a>
    </ul>

    <form action="">
        <button type="submit" class="flex items-center gap-2 text-md uppercase bg-[#005382] p-2 text-white rounded-lg w-full"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
    </form>
</nav>

<script>
    var currentLocation = window.location.href;
    var navLinks = document.querySelectorAll("nav a");
    navLinks.forEach(function (link) {
        if (link.href === currentLocation) {
            link.classList.add("active");
        }
    });
</script>

