<div class="block lg:hidden">
    <i class="fa-solid fa-bars text-3xl hover:cursor-pointer" onclick="sidebar()"></i>
</div>

<div class="sidebar fixed top-0 -left-48 w-0 h-full flex flex-col bg-white z-20 gap-2 transition-all duration-500" id="sidebar">
    <div class="p-3 flex flex-col relative">
        <img src="{{ asset('image/Logowname.png') }}" alt="" class="w-[130px] self-center">

        <div onclick="sidebar()" class="w-14 h-14 z-40 flex items-center justify-center absolute top-0 right-0 rounded-md hover:cursor-pointer">
            <span class="text-4xl text-black/80 ">&times;</span>
        </div>
    </div>
    
    <ul class="list-none flex flex-col py-2 gap-[1px] overflow-y-auto">

        {{-- DASHBOARD (ALL ROLES) --}}
        <div class="text-[13px] capitalize p-1 w-full text-gray-500 font-semibold flex items-center justify-between gap-2">Home</div>
        <li>
            <a href="{{ route('admin.dashboard') }}" class="mt-1 flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 relative hover:bg-gray-100 hover:text-black">
                <i class="fa-regular fa-chart-fft text-base"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- ADMIN & SUPERADMIN MENUS --}}
        @if (auth('superadmin')->check() || auth('admin')->check())
            <li class="">
                <a href="{{ route('admin.sales') }}" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 relative hover:bg-gray-100 hover:text-black {{ request()->is('admin/sales*') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-file-chart-column text-base"></i>
                    <span>Sales Reports</span>
                </a>
            </li>

            <div class="text-[13px] capitalize p-1 w-full text-gray-500 font-semibold mt-2 flex items-center justify-between">Communication</div>
            <li class="">
                <a href="{{ route('admin.chat.index') }}" id="chatNav" class="mt-1 relative flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('admin/chat*') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-comment-captions text-base"></i>
                    <span>Chat</span>
                    @if ($adminsidebar_counter > 0)
                        <span class="absolute -top-1 right-1 bg-red-500 px-1 rounded-full text-xs text-white">
                            {{ $adminsidebar_counter }}
                        </span>
                    @endif
                </a>
            </li>

            <div class="text-[13px] capitalize p-1 w-full text-gray-500 font-semibold mt-2 flex items-center justify-between">Management</div>
            <li class="">
                <a href="{{ route('admin.inventory') }}" class="mt-1 flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is(['admin/inventory', 'admin/ocr-files']) ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-warehouse text-base"></i>
                    <span>Inventory</span>
                </a>
            </li>
            
            <li class="">
                <a href="{{ route('admin.productlisting') }}" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('admin/productlisting') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-building-memo text-base"></i>
                    <span>Product Deals</span>
                </a>
            </li>

            <li class="">
                <a href="{{ route('admin.order') }}" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('admin/order*') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-cart-circle-check text-base"></i>
                    <span>Orders</span>
                </a>
            </li>

            <li class="">
                <a href="{{ route('superadmin.account.index') }}" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('manageaccounts*') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-users-gear text-base"></i>
                    <span>Manage Accounts</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.contentmanagement') }}" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('admin/contentmanagement') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-file-circle-plus text-base"></i>
                    <span>Manage Content</span>
                </a>
            </li>

            <li>
                <a href="{{ route('superadmin.reviews.index') }}" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('superadmin/reviews*') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-star-sharp-half-stroke text-base"></i>
                    <span>Review Manager</span>
                </a>
            </li>

            <div class="text-[13px] capitalize p-1 w-full text-gray-500 font-semibold mt-2 flex items-center justify-between">History & Staff</div>
            
            <li class="">
                <a href="{{ route('admin.history') }}" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('admin/history') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-clock-rotate-left text-base"></i>
                    <span>Order History</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.historylog') }}" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('admin/historylog') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-circle-user-clock text-base"></i>
                    <span>History Log</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.stafflocation') }}" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('admin/stafflocation') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-location-dot text-base"></i>
                    <span>Staff Location</span>
                </a>
            </li>
        @endif

        {{-- STAFF MENUS --}}
        @if (auth('staff')->check())
            <li class="">
                <a href="{{ route('admin.order') }}" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('admin/order*') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-cart-circle-check text-base"></i>
                    <span>Orders</span>
                </a>
            </li>

            <li class="">
                <a href="{{ route('admin.chat.index') }}" id="chatNav" class="relative flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('admin/chat*') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                    <i class="fa-regular fa-comment-captions text-base"></i>
                    <span>Chat</span>
                    @if ($adminsidebar_counter > 0)
                        <span class="absolute -top-1 right-1 bg-red-500 px-1 rounded-full text-xs text-white">
                            {{ $adminsidebar_counter }}
                        </span>
                    @endif
                </a>
            </li>
        @endif
    </ul>

    {{-- LOGOUT --}}
    @if (Auth::guard('superadmin')->check())
        <form id="logout-form" method="POST" action="{{ route('superadmin.logout') }}" class="mt-auto">
            @csrf
            <button type="submit" class="logout w-full text-sm text-left flex items-center gap-4 p-4 font-regular transition-all duration-300 relative bg-gray-100 text-black border-l-4 border-blue-600 hover:bg-gray-200">
                <i class="fa-medium fa-right-from-bracket text-base"></i>
                <span>Logout ...</span>
            </button>
        </form>
    @elseif (Auth::guard('admin')->check())
        <form id="logout-form" method="POST" action="{{ route('admin.logout') }}" class="mt-auto">
            @csrf
            <button type="submit" class="logout w-full text-sm text-left flex items-center gap-4 p-4 font-medium transition-all duration-300 relative bg-gray-100 text-black border-l-4 border-blue-600 hover:bg-gray-200">
                <i class="fa-medium fa-right-from-bracket text-base"></i>
                <span>Logout ...</span>
            </button>
        </form>
    @elseif (Auth::guard('staff')->check())
        <form id="logout-form" method="POST" action="{{ route('staff.logout') }}" class="mt-auto">
            @csrf
            <button type="submit" class="logout w-full text-sm text-left flex items-center gap-4 p-4 font-medium transition-all duration-300 relative bg-gray-100 text-black border-l-4 border-blue-600 hover:bg-gray-200">
                <i class="fa-medium fa-right-from-bracket text-base"></i>
                <span>Logout ...</span>
            </button>
        </form>
    @endif
</div>
<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden z-10"
     >
</div>
<script src="{{ asset('js/burgermenu.js') }}"></script>

{{-- <script src="{{asset('js/burgermenu.js')}}"></script> --}}
