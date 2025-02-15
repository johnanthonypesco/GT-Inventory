@props(['name'=> 'Jewel Velasquez', 'message' => 'Hello, how can I help you?', 'date' => '2021-10-10', 'unread' => 1])

<div class="bg-none lg:bg-white w-full mt-3 p-3 rounded-lg flex flex-col lg:flex-row items-center gap-3 relative cursor-pointer hover:animate-pulse">
    <i class="fa-solid fa-user text-[30px] bg-[#005382] text-white p-5 rounded-full"></i>
    <div>
        <p class="font-semibold text-[15px] lg:text-[25px] text-center lg:text-left">{{$name}}</p>
        <p class="text-[23px] font-regular text-black/80 hidden lg:block">{{$message}}</p>
    </div>

    <div class="absolute right-2 top-2 flex flex-col gap-1 hidden lg:block">
        <p class="text-[18px] font-semibold">{{$date}}</p>
        <p class="bg-red-600/80 w-[27px] h-fit p-1 text-white rounded-full text-center text-sm">{{$unread}}</p>
    </div>
</div>