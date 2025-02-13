<div class="block md:hidden">
    <i class="fa-solid fa-bars text-3xl hover:cursor-pointer" onclick="sidebar()"></i>
</div>

<div class="sidebar fixed top-0 -left-20 w-0 h-full flex flex-col bg-white z-20 gap-5 p-10 transition-all duration-500 overflow-hidden">
    <a href="{{ route ('staff.dashboard')}}" class="text-md"><i class="fa-solid fa-gauge text-[#005382] text-md"></i>Dashboard</a>
    <a href="{{ route ('staff.inventory')}}" class="text-md"><i class="fa-solid fa-boxes-stacked text-[#005382] text-md"></i>Inventory</a>
    <a href="{{ route ('staff.order')}}" class="text-md"><i class="fa-solid fa-cart-shopping text-[#005382] text-md"></i>Orders</a>
    <a href="{{ route ('staff.chat')}}" class="text-md"><i class="fa-solid fa-message text-[#005382] text-md"></i>Chat</a>
    <a href="{{ route ('staff.history')}}" class="text-md"><i class="fa-solid fa-clock-rotate-left text-[#005382] text-md"></i>History</a>
    <form action="" class="mt-auto">
        <button type="submit" class="flex items-center gap-2 text-md uppercase bg-[#005382] p-2 text-white rounded-lg w-full"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
    </form>
</div>

<script src="{{asset('js/burgermenu.js')}}"></script>