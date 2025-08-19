<div class="block lg:hidden">
    <i class="fa-solid fa-bars text-3xl hover:cursor-pointer" onclick="sidebar()"></i>
</div>

<div class="sidebar fixed top-0 -left-48 w-0 h-full flex flex-col bg-white z-20 gap-1 p-10 transition-all duration-500" id="sidebar">
    <div class="p-3 flex flex-col relative">
        <img src="{{ asset('image/Logowname.png') }}" alt="" class="w-[130px] self-center">

<div onclick="sidebar()" class="w-14 h-14 bg-[#005382] shadow-md z-40 flex items-center justify-center absolute -top-10 -right-14 rounded-md hover:cursor-pointer">
    <span class="text-6xl text-white ">&times;</span>
</div>
    </div>
    <ul class="list-none flex flex-col px-1 py-0 gap-[1px] overflow-y-scroll">

        {{-- DASHBOARD (ALL ROLES) --}}
        <div class="text-[12px] uppercase p-1 w-full text-[#005382] border-b-2 font-semibold">Home</div>
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

            <div class="text-[12px] uppercase p-1 w-full text-[#005382] border-b-2 font-semibold mt-2">Communication</div>
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

            <div class="text-[12px] uppercase p-1 w-full text-[#005382] border-b-2 font-semibold mt-2">Management</div>
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

            <div class="text-[12px] uppercase p-1 w-full text-[#005382] border-b-2 font-semibold mt-2">History & Staff</div>
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
  @if (Auth::guard('superadmin')->check())
    </ul>
    
    <form id="logout-form" method="POST" action="{{ route('superadmin.logout') }}" class="mt-auto">
        @csrf
        <button type="submit" class="text-md text-left flex items-center gap-2 logout w-full">
            <i class="fa-solid fa-right-from-bracket text-white text-md"></i>Logout
        </button>
    </form>

@elseif (Auth::guard('admin')->check())
    <form id="logout-form" method="POST" action="{{ route('admin.logout') }}" class="mt-auto">
        @csrf
        <button type="submit" class="text-md text-left flex items-center gap-2 logout w-full">
            <i class="fa-solid fa-right-from-bracket text-white text-md"></i>Logout
        </button>
    </form>

@elseif (Auth::guard('staff')->check())
    <form id="logout-form" method="POST" action="{{ route('staff.logout') }}" class="mt-auto">
        @csrf
        <button type="submit" class="text-md text-left flex items-center gap-2 logout w-full">
            <i class="fa-solid fa-right-from-bracket text-white text-md"></i>Logout
        </button>
    </form>

@elseif (Auth::guard('web')->check())
    {{-- This handles the regular 'user' --}}
    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="mt-auto">
        @csrf
        <button type="submit" class="text-md text-left flex items-center gap-2 logout w-full">
            <i class="fa-solid fa-right-from-bracket text-white text-md"></i>Logout
        </button>
    </form>
@endif
</div>
<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden z-10"
     onclick="sidebar()">
</div>
<script src="{{ asset('js/burgermenu.js') }}"></script>

<script src="{{asset('js/burgermenu.js')}}"></script>
