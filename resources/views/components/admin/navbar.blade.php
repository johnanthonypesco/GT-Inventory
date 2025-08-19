<nav id="sidebar" class="md:w-[15%] w-full hidden lg:flex flex-col p-2 fixed opacity-0 h-screen shadow-sm">
    <div class="p-2 flex flex-col">
        <img src="{{ asset('image/Logowname.png') }}" alt="" class="w-[110px] self-center">
    </div>

    <ul class="list-none flex flex-col px-1 py-0 gap-[1px]">

        {{-- DASHBOARD (ALL ROLES) --}}
        <div class="text-[12px] uppercase p-1 w-full text-[#005382] border-b font-semibold flex items-center justify-between">Home</i></div>
        <li>
            <a href="{{ route('admin.dashboard') }}" class="mt-1 flex items-center gap-1 p-2">
                <i class="fa-solid fa-gauge"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- ADMIN & SUPERADMIN MENUS --}}
        @if (auth('superadmin')->check() || auth('admin')->check())
            <li class="{{ request()->is('admin/sales*') }}">
                <a href="{{ route('admin.sales') }}" class="flex items-center gap-1 p-2">
                    <i class="fa-solid fa-print"></i>
                    <span>Sales Reports</span>
                </a>
            </li>

            <div class="text-[12px] uppercase p-1 w-full text-[#005382] border-b font-semibold mt-2 flex items-center justify-between">Communication</i></div>
            <li class="{{ request()->is('admin/chat*') }}">
                <a href="{{ route('admin.chat.index') }}" id="chatNav" class="mt-1 relative flex items-center gap-1 p-2">
                    <i class="fa-brands fa-rocketchat"></i>
                    <span>Chat</span>
                    @if ($adminsidebar_counter > 0)
                        <span class="absolute -top-1 right-1 bg-red-500 px-1 rounded-full text-[12px]">
                            {{ $adminsidebar_counter }}
                        </span>
                    @endif
                </a>
            </li>

            <div class="text-[12px] uppercase p-1 w-full text-[#005382] border-b font-semibold mt-2 flex items-center justify-between">Management</i></div>
            <li class="{{ request()->is(['admin/inventory', 'admin/ocr-files']) }}">
                <a href="{{ route('admin.inventory') }}" class="mt-1 flex items-center gap-1 p-2">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Inventory</span>
                </a>
            </li>
            
            <li class="{{ request()->is('admin/productlisting') }}">
                <a href="{{ route('admin.productlisting') }}" class="flex items-center gap-1 p-2">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Product Deals</span>
                </a>
            </li>

            <li class="{{ request()->is('admin/order*') }}">
                <a href="{{ route('admin.order') }}" class="flex items-center gap-1 p-2">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Orders</span>
                </a>
            </li>

            <li class="{{ request()->is('manageaccounts*') }}">
                <a href="{{ route('superadmin.account.index') }}" class="flex items-center gap-1 p-2">
                    <i class="fa-solid fa-bars-progress"></i>
                    <span>Manage Accounts</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.contentmanagement') }}" class="flex items-center gap-1 p-2">
                    <i class="fa-solid fa-file"></i>
                    <span>Manage Content</span>
                </a>
            </li>

            <li>
                <a href="{{ route('superadmin.reviews.index') }}" class="flex items-center gap-1 p-2">
                    <i class="fa-solid fa-star"></i>
                    <span>Review Manager</span>
                </a>
            </li>

            <div class="text-[12px] uppercase p-1 w-full text-[#005382] border-b font-semibold mt-2 flex items-center justify-between">History & Staff</i></div>
            <li class="{{ request()->is('admin/history*') }}">
                <a href="{{ route('admin.history') }}" class="fmt-1 lex items-center gap-1 p-2">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Order History</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.historylog') }}" class="flex items-center gap-1 p-2">
                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                    <span>History Log</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.stafflocation') }}" class="flex items-center gap-1 p-2">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>Staff Location</span>
                </a>
            </li>
        @endif

        {{-- STAFF MENUS --}}
        @if (auth('staff')->check())
            <li class="{{ request()->is('staff/order') }}">
                <a href="{{ route('admin.order') }}" class="flex items-center gap-1 p-2">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Orders</span>
                </a>
            </li>

            <li class="{{ request()->is('admin/chat*') }}">
                <a href="{{ route('admin.chat.index') }}" id="chatNav" class="relative flex items-center gap-1 p-2">
                    <i class="fa-brands fa-rocketchat"></i>
                    <span>Chat</span>
                    @if ($adminsidebar_counter > 0)
                        <span class="absolute -top-1 right-1 bg-red-500 px-1 rounded-full text-[12px]">
                            {{ $adminsidebar_counter }}
                        </span>
                    @endif
                </a>
            </li>
        @endif
    </ul>

       @if (Auth::guard('superadmin')->check())
    {{-- LOGOUT --}}
    <div class="mt-auto px-1">
        <form id="logout-form" method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="logout text-sm text-left flex items-center gap-1 p-2 w-full">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>

        @elseif (Auth::guard('admin')->check())
            <form id="logout-form" method="POST" action="{{ route('admin.logout') }}" class="mt-auto">
                @csrf
                <button type="submit" class="text-sm text-left flex items-center gap-2 logout w-full sm:justify-center lg:justify-start">
                    <i class="fa-solid sm:text-2xl lg:text-sm fa-right-from-bracket text-white text-sm"></i><span class="sm:hidden lg:inline-block">Logout</span>
                </button>
            </form>

        @elseif (Auth::guard('staff')->check())
            <form id="logout-form" method="POST" action="{{ route('staff.logout') }}" class="mt-auto">
                @csrf
                <button type="submit" class="text-sm text-left flex items-center gap-2 logout w-full sm:justify-center lg:justify-start">
                    <i class="fa-solid sm:text-2xl lg:text-sm fa-right-from-bracket text-white text-sm"></i><span class="sm:hidden lg:inline-block">Logout</span>
                </button>
            </form>
        @endif
    </li>
    </div>
</nav>

<script src="{{ asset('js/navbar.js') }}"></script>
