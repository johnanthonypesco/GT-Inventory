<nav class="md:w-[18%] w-full hidden md:flex flex-col p-3">
    <div class="p-3 flex flex-col">
        <img src="{{asset ('image/Group 39.png')}}" alt="" class="w-[180px] self-center">
        <hr class="mt-2">
    </div>

    <li class="list-none flex flex-col p-2 gap-[20px] relative h-full">
        <a href="{{ route ('staff.dashboard')}}" class="text-md"><i class="fa-solid fa-gauge text-[#005382] text-md"></i>Dashboard</a>
        <a href="{{ route ('staff.inventory')}}" class="text-md"><i class="fa-solid fa-boxes-stacked text-[#005382] text-md"></i>Inventory</a>
        <a href="{{ route ('staff.order')}}" class="text-md"><i class="fa-solid fa-cart-shopping text-[#005382] text-md"></i>Orders</a>
        <a href="{{ route ('staff.chat')}}" class="text-md"><i class="fa-solid fa-message text-[#005382] text-md"></i>Chat</a>
        <a href="{{ route ('staff.history')}}" class="text-md"><i class="fa-solid fa-clock-rotate-left text-[#005382] text-md"></i>History</a>
        <form action="" class="mt-auto">
            <button type="submit" class="text-md text-left flex items-center gap-2 logout w-full"><i class="fa-solid fa-right-from-bracket text-white text-md"></i>Logout</button>
        </form>    
    </li>
</nav>

<script src="{{asset ('js/navbar.js')}}"></script>
