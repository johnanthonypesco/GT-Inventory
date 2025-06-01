<nav class="md:w-[16%] w-full hidden md:flex flex-col p-3 fixed top-4 left-4">
    <div class="p-3 flex flex-col">
        <img src="{{ asset('image/Logowname.png') }}" alt="" class="w-[180px] self-center">
        <hr class="mt-2">
    </div>

    <li class="list-none flex flex-col p-2 gap-[8px] h-full">
        <a href="{{ route('admin.dashboard') }}" class="text-md">
            <i class="fa-solid fa-gauge text-[#005382] text-md"></i>Dashboard
        </a>

        @if (auth('superadmin')->check() || auth('admin')->check())
            <a href="{{ route('admin.inventory') }}" class="text-md">
                <i class="fa-solid fa-boxes-stacked text-[#005382] text-md"></i>Inventory
            </a>
            <a href="{{ route('admin.sales') }}" class="text-md">
                <i class="fa-solid fa-print text-[#005382] text-md"></i>Sales Reports
            </a>
            
            <a href="{{ route('admin.productlisting') }}" class="text-md">
                <i class="fa-solid fa-list-check text-[#005382] text-md"></i>Product Deals
            </a>
            <a href="{{ route('admin.chat.index') }}" id="chatNav" class="text-md relative">
                <i class="fa-brands fa-rocketchat text-[#005382]"></i>Chat
                @if ($adminsidebar_counter > 0)
                    <span class="absolute top-2.5 right-2 bg-red-500 text-white p-1 px-2 rounded-full text-xs">
                        {{ $adminsidebar_counter }}
                    </span>
                @endif
            </a>
            
            <a href="{{ route('superadmin.account.index') }}" class="text-md whitespace-nowrap">
                <i class="fa-solid fa-bars-progress text-[#005382] text-md"></i>Manage Accounts
            </a>
            
            <a href="{{ route('admin.order') }}" class="text-md">
                <i class="fa-solid fa-cart-shopping text-[#005382] text-md"></i>Orders
            </a>

            <a href="{{ route('admin.history') }}" class="text-md">
                <i class="fa-solid fa-clock-rotate-left text-[#005382] text-md"></i>Order History
            </a>
            

            <a href="{{ route ('admin.historylog')}}" class="text-md"><i class="fa-solid fa-magnifying-glass-chart text-[#005382] text-md"></i>History Log</a>

            <a href="{{ route ('admin.stafflocation')}}" class="text-md"><i class="fa-solid fa-map-location-dot text-[#005382] text-md"></i>Staff Location</a>
        @endif

        @if (auth('staff')->check())
            <a href="{{ route('admin.order') }}" class="text-md">
                <i class="fa-solid fa-cart-shopping text-[#005382] text-md"></i>Orders
            </a>

            <a href="{{ route('admin.chat.index') }}" id="chatNav" class="text-md relative">
                <i id="navBarCounter" class="fa-brands fa-rocketchat"></i>Chat
                @if ($adminsidebar_counter > 0)
                    <span class="absolute top-2.5 right-2 bg-red-500 text-white p-1 px-2 rounded-full text-xs">
                        {{ $adminsidebar_counter }}
                    </span>
                @endif
            </a>
        @endif

        <form id="logout-form" method="POST" action="{{ route('user.logout') }}" class="mt-auto">
            @csrf
            <button type="submit" class="text-md text-left flex items-center gap-2 logout w-full">
                <i class="fa-solid fa-right-from-bracket text-white text-md"></i>Logout
            </button>
        </form>    
    </li>
</nav>

<script src="{{ asset('js/navbar.js') }}"></script>
<a href="{{ route('superadmin.account.index') }}" class="text-md whitespace-nowrap">