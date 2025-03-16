@props(['currentPage' => 1, 'totalPage' => 1, 'prev' => '#', 'next' => '#'])

<div class="flex justify-between items-center mt-6">
    <p>
        Showing <span class="bg-[#005382] text-white px-2 py-1 rounded-lg">{{$currentPage}}</span> 
        out of <span>{{$totalPage}}</span>
    </p>
    <div class="flex gap-2">
        <a href="{{$prev}}" 
            class="bg-[#005382] text-white px-3 py-1 rounded-lg cursor-pointer 
                   hover:bg-[#004066] transition duration-300 
                   {{ $currentPage == 1 ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
            Prev
        </a>

        <a href="{{$next}}" 
            class="bg-[#005382] text-white px-3 py-1 rounded-lg cursor-pointer 
                   hover:bg-[#004066] transition duration-300 
                   {{ $currentPage == $totalPage ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
            Next
        </a>
    </div>
</div>
