<nav class="md:w-[18%] w-full hidden md:flex flex-col p-3">
    <div class="p-3 flex flex-col">
        <img src="{{asset ('image/Logowname.png')}}" alt="" class="w-[180px] self-center">
        <hr class="mt-2">
    </div>

    <li class="list-none flex flex-col p-2 gap-[20px] h-full">
        <a href="{{ route ('admin.dashboard')}}" class="text-md"><i class="fa-solid fa-gauge text-[#005382] text-md"></i>Dashboard</a>
        <a href="{{ route ('admin.inventory')}}" class="text-md"><i class="fa-solid fa-boxes-stacked text-[#005382] text-md"></i>Inventory</a>
        <a href="{{ route ('admin.order')}}" class="text-md"><i class="fa-solid fa-cart-shopping text-[#005382] text-md"></i>Orders</a>
        <a href="{{ route ('admin.chat')}}" class="text-md"><i class="fa-solid fa-message text-[#005382] text-md"></i>Chat</a>
        <a href="{{ route ('superadmin.account.index')}}" class="text-md whitespace-nowrap"><i class="fa-solid fa-bars-progress text-[#005382] text-md"></i>Manage Account</a>
        <a href="{{ route ('admin.productlisting')}}" class="text-md"><i class="fa-solid fa-list-check text-[#005382] text-md"></i>Product Deals</a>
        <a href="{{ route ('admin.history')}}" class="text-md"><i class="fa-solid fa-clock-rotate-left text-[#005382] text-md"></i>Order History</a>
        
        <form action="" class="mt-auto">
            <button type="submit" class="text-md text-left flex items-center gap-2 logout w-full"><i class="fa-solid fa-right-from-bracket text-white text-md"></i>Logout</button>
        </form>    
    </li>
</nav>

<script src="{{asset ('js/navbar.js')}}"></script>
<a href="{{ route('superadmin.account.index') }}" class="text-md whitespace-nowrap">
