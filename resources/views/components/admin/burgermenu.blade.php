<div class="block md:hidden">
    <i class="fa-solid fa-bars text-3xl hover:cursor-pointer" onclick="sidebar()"></i>
</div>

<div class="sidebar fixed top-0 -left-20 w-0 h-full flex flex-col bg-white z-20 gap-5 p-10 transition-all duration-500 overflow-hidden">
    <a href="{{ route ('admin.dashboard')}}" class="text-md"><i class="fa-solid fa-gauge text-[#005382] text-md"></i>Dashboard</a>

    @if (auth('superadmin')->check())
        <a href="{{ route ('admin.inventory')}}" class="text-md"><i class="fa-solid fa-boxes-stacked text-[#005382] text-md"></i>Inventory</a>
        <a href="{{ route ('admin.sales')}}" class="text-md"><i class="fa-solid fa-print text-[#005382] text-md"></i>Sales Reports</a>
        <a href="{{ route ('admin.order')}}" class="text-md"><i class="fa-solid fa-cart-shopping text-[#005382] text-md"></i>Orders</a>

        <a href="{{ route('admin.chat.index') }}" id="chatNav" class="text-md relative">
            <i class="fa-brands fa-rocketchat text-[#005382] text-md"></i>Chat
            @if ($adminsidebar_counter > 0)
                <span class="absolute top-2.5 right-2 bg-red-500 text-white p-1 px-2 rounded-full text-xs">
                    {{ $adminsidebar_counter }}
                </span>
            @endif
        </a>

        <a href="{{ route ('superadmin.account.index')}}" class="text-md whitespace-normal"><i class="fa-solid fa-bars-progress text-[#005382] text-md"></i>Manage Account</a>

        <a href="{{ route ('admin.productlisting')}}" class="text-md"><i class="fa-solid fa-list-check text-[#005382] text-md"></i>Product Deals</a>

        <a href="{{ route ('admin.history')}}" class="text-md"><i class="fa-solid fa-clock-rotate-left text-[#005382] text-md"></i>Order History</a>

        <a href="{{ route ('admin.historylog')}}" class="text-md"><i class="fa-solid fa-clock-rotate-left text-[#005382] text-md"></i>History Log</a>
        <a href="{{ route ('admin.contentmanagement')}}" class="text-sm"><i class="fa-solid fa-file text-[#005382] text-sm"></i>Manage Content</a>


    @endif

    @if (auth('staff')->check())
        <a href="{{ route ('admin.order')}}" class="text-md"><i class="fa-solid fa-cart-shopping text-[#005382] text-md"></i>Orders</a>

        <a href="{{ route('admin.chat.index') }}" id="headerCounter" class="text-md relative">
            <i class="fa-brands fa-rocketchat text-[#005382]"></i>Chat
            @if ($adminsidebar_counter > 0)
                <span class="absolute top-2.5 right-2 bg-red-500 text-white p-1 px-2 rounded-full text-xs">
                    {{ $adminsidebar_counter }}
                </span>
            @endif
        </a>
    @endif

    <form id="logout-form" method="POST" action="{{ route('user.logout') }}" class="mt-auto">
        @csrf

        <button type="submit" class="text-md text-left flex items-center gap-2 logout w-full"><i class="fa-solid fa-right-from-bracket text-white text-md"></i>Logout</button>
    </form>
</div>

<script src="{{asset('js/burgermenu.js')}}"></script>