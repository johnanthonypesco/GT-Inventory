<div class="block md:hidden">
    <i class="fa-solid fa-bars text-3xl hover:cursor-pointer" onclick="sidebar()"></i>
</div>

<div class="sidebar fixed top-0 -left-20 w-0 h-full flex flex-col bg-white z-20 gap-5 p-10 transition-all duration-500 overflow-hidden">
    <a href="{{ route ('admin.dashboard')}}" class="text-md"><i class="fa-solid fa-gauge text-[#005382] text-md"></i>Dashboard</a>

    @if (auth('superadmin')->check())
        <a href="{{ route ('admin.inventory')}}" class="text-md"><i class="fa-solid fa-boxes-stacked text-[#005382] text-md"></i>Inventory</a>

        <a href="{{ route ('admin.order')}}" class="text-md"><i class="fa-solid fa-cart-shopping text-[#005382] text-md"></i>Orders</a>

        <a href="{{ route ('employee.chat')}}" class="text-md"><i class="fa-solid fa-message text-[#005382] text-md"></i>Chat</a>

        <a href="{{ route ('superadmin.account.index')}}" class="text-md whitespace-nowrap"><i class="fa-solid fa-bars-progress text-[#005382] text-md"></i>Manage Account</a>

        <a href="{{ route ('admin.productlisting')}}" class="text-md"><i class="fa-solid fa-list-check text-[#005382] text-md"></i>Product Deals</a>

        <a href="{{ route ('admin.history')}}" class="text-md"><i class="fa-solid fa-clock-rotate-left text-[#005382] text-md"></i>Order History</a>
    @endif

    @if (auth('staff')->check())
        <a href="{{ route ('admin.order')}}" class="text-md"><i class="fa-solid fa-cart-shopping text-[#005382] text-md"></i>Orders</a>

        <a href="{{ route ('employee.chat')}}" class="text-md"><i class="fa-solid fa-message text-[#005382] text-md"></i>Chat</a>
    @endif

    <form id="logout-form" method="POST" action="{{ route('user.logout') }}" class="mt-auto">
        @csrf

        <button type="submit" class="text-md text-left flex items-center gap-2 logout w-full"><i class="fa-solid fa-right-from-bracket text-white text-md"></i>Logout</button>
    </form>
</div>

<script src="{{asset('js/burgermenu.js')}}"></script>