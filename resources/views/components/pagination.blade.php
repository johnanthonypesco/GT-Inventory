@props(['currentPage' => 1, 'totalPage' => 1, 'prev' =>'#', 'next'=>'#'])

<div class="flex justify-between mt-6 opacity-50">
    <p>Showing <span class="bg-[#005382] text-white px-2 py-1 rounded-lg">{{$currentPage}}</span> out of <span>{{$totalPage}}</span></p>
    <div class="flex gap-2">
        <a href="{{$prev}}" class="bg-[#005382] text-white px-3 py-1 rounded-lg cursor-pointer hover:bg-[#004066] transition duration-300 {{$prev == 1 ? 'opacity-50 cursor-not-allowed' : ''}}">Prev</a>
        <a href="{{$next}}" class="bg-[#005382] text-white px-3 py-1 rounded-lg cursor-pointer hover:bg-[#004066] transition duration-300 {{$next == $currentPage ? 'opacity-50 cursor-not-allowed' : ''}}">Next</a>
    </div>
</div> 