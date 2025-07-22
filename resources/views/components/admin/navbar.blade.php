<nav id="sidebar" class="md:w-[15%] w-full hidden md:flex flex-col p-3 fixed top-4 left-4">
    <div class="p-3 flex flex-col">
        <img src="{{ asset('image/Logowname.png') }}" alt="" class="w-[130px] self-center">
        <hr class="mt-2">
    </div>

    <li class="list-none flex flex-col p-2 gap-[3px] h-full">
        <a href="{{ route('admin.dashboard') }}" class="text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2">
            <i class="fa-solid fa-gauge text-[#005382] text-sm sm:text-2xl lg:text-sm"></i>
            <span class="sm:hidden lg:inline-block">Dashboard</span>
        </a>

        @if (auth('superadmin')->check() || auth('admin')->check())
            <a href="{{ route('admin.inventory') }}" class="text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2 {{ request()->is('admin/inventory') ? 'active' : ''  }}">
                <i class="fa-solid sm:text-2xl lg:text-sm fa-boxes-stacked text-[#005382] text-sm {{ request()->is('admin/inventory') ? 'text-white' : ''  }}"></i><span class="sm:hidden lg:inline-block">Inventory</span>

            </a>
            <a href="{{ route('admin.sales') }}" class="text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2">
                <i class="fa-solid sm:text-2xl lg:text-sm fa-print text-[#005382] text-sm"></i>
                <span class="sm:hidden lg:inline-block">Sales Report</span>

            </a>
            
            <a href="{{ route('admin.productlisting') }}" class="text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2 {{ request()->is('admin/productlisting') ? 'active' : ''  }}">
                <i class="fa-solid sm:text-2xl lg:text-sm fa-list-check text-[#005382] text-sm {{ request()->is('admin/productlisting') ? 'text-white' : ''  }}"></i>
                <span class="sm:hidden lg:inline-block">Product Deals</span>
            </a>
            <a href="{{ route('admin.chat.index') }}" id="chatNav" class="relative text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2">
                <i class="fa-brands fa-rocketchat text-[#005382]"></i>
                <span class="sm:hidden lg:inline-block">Chat</span>
                @if ($adminsidebar_counter > 0)
                    <span class="absolute top-2.5 right-2 bg-red-500 text-white p-1 px-2 rounded-full text-xs">
                        {{ $adminsidebar_counter }}
                    </span>
                @endif
            </a>
            
            <a href="{{ route('superadmin.account.index') }}" class="text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2">
                <i class="fa-solid sm:text-2xl lg:text-sm fa-bars-progress text-[#005382] text-sm"></i>
                <span class="sm:hidden lg:inline-block">Manage Accounts</span>

            </a>
            
            <a href="{{ route('admin.order') }}" class="text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2">
                <i class="fa-solid sm:text-2xl lg:text-sm fa-cart-shopping text-[#005382] text-sm"></i>
                <span class="sm:hidden lg:inline-block">Orders</span>

            </a>

            <a href="{{ route('admin.history') }}" class="text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2">
                <i class="fa-solid sm:text-2xl lg:text-sm fa-clock-rotate-left text-[#005382] text-sm"></i>
                <span class="sm:hidden lg:inline-block">Order History</span>

            </a>
            

            <a href="{{ route ('admin.historylog')}}" class="text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2"><i class="fa-solid sm:text-2xl lg:text-sm fa-magnifying-glass-chart text-[#005382] text-sm"></i><span class="sm:hidden lg:inline-block">History Log</span></a>

            <a href="{{ route ('admin.stafflocation')}}" class="text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2"><i class="fa-solid sm:text-2xl lg:text-sm fa-map-location-dot text-[#005382] text-sm"></i><span class="sm:hidden lg:inline-block">Staff Location</span></a>
            <a href="{{ route ('admin.contentmanagement')}}" class="text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2"><i class="fa-solid sm:text-2xl lg:text-sm fa-file text-[#005382] text-sm"></i><span class="sm:hidden lg:inline-block">Manage Content</span></a>


            <a href="{{ route('superadmin.reviews.index') }}" class="text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2"><i class="fa-solid sm:text-2xl lg:text-sm fa-star text-[#005382] text-sm"></i><span class="sm:hidden lg:inline-block">Review Manager</span></a>
        @endif

        @if (auth('staff')->check())
            <a href="{{ route('admin.order') }}" class="text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2">
                <i class="fa-solid sm:text-2xl lg:text-sm fa-cart-shopping text-[#005382] text-sm"></i>
                <span class="sm:hidden lg:inline-block">Orders</span>
            </a>

            <a href="{{ route('admin.chat.index') }}" id="chatNav" class="relative text-sm sm:flex sm:justify-center lg:flex lg:justify-start items-center gap-2">
                <i id="navBarCounter" class="fa-brands fa-rocketchat text-sm sm:text-2xl lg:text-sm"></i><span class="sm:hidden lg:inline-block">Chat</span>
                @if ($adminsidebar_counter > 0)
                    <span class="absolute top-2.5 right-2 bg-red-500 text-white p-1 px-2 rounded-full text-xs">
                        {{ $adminsidebar_counter }}
                    </span>
                @endif
            </a>
        @endif

        <form id="logout-form" method="POST" action="{{ route('user.logout') }}" class="mt-auto">
            @csrf
            <button type="submit" class="text-sm text-left flex items-center gap-2 logout w-full sm:justify-center lg:justify-start">
                <i class="fa-solid sm:text-2xl lg:text-sm fa-right-from-bracket text-white text-sm"></i><span class="sm:hidden lg:inline-block">Logout</span>
            </button>
        </form>    
    </li>
</nav>

<script src="{{ asset('js/navbar.js') }}"></script>
