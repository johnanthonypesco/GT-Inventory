<nav class="md:w-[18%] w-full hidden md:flex flex-col p-3">
    <div class="p-3 flex flex-col">
        <img src="{{ asset('image/Group 39.png') }}" alt="Logo" class="w-[180px] self-center">
        <hr class="mt-2">
    </div>

    <ul class="list-none flex flex-col p-2 gap-[20px] h-full">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="text-md">
                <i class="fa-solid fa-gauge text-[#005382] text-md"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.inventory') }}" class="text-md">
                <i class="fa-solid fa-boxes-stacked text-[#005382] text-md"></i> Inventory
            </a>
        </li>
        <li>
            <a href="{{ route('admin.order') }}" class="text-md">
                <i class="fa-solid fa-cart-shopping text-[#005382] text-md"></i> Orders
            </a>
        </li>
        <li>
            <a href="{{ route('admin.chat') }}" class="text-md">
                <i class="fa-solid fa-message text-[#005382] text-md"></i> Chat
            </a>
        </li>
        <li>
            <a href="{{ route('superadmin.account.index') }}" class="text-md whitespace-nowrap">
                <i class="fa-solid fa-bars-progress text-[#005382] text-md"></i> Manage Accounts
            </a>
        </li>
        <li>
            <a href="{{ route('admin.productlisting') }}" class="text-md">
                <i class="fa-solid fa-list text-[#005382] text-md"></i> Product Listing
            </a>
        </li>
        <li>
            <a href="{{ route('admin.history') }}" class="text-md">
                <i class="fa-solid fa-clock-rotate-left text-[#005382] text-md"></i> History
            </a>
        </li>
        <li class="mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-md text-left flex items-center gap-2 logout w-full">
                    <i class="fa-solid fa-right-from-bracket text-white text-md"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</nav>

<script src="{{ asset('js/navbar.js') }}"></script>
