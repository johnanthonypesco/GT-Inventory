@php use Carbon\Carbon; @endphp
<div class="fixed w-full h-screen top-0 left-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden" id="viewarchivedstocksmodal">
      <div class="modal bg-white rounded-lg w-full max-w-4xl max-h-screen overflow-y-auto p-6">
        <div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-4 sticky top-0 bg-white z-10">
          <p class="text-xl font-medium text-gray-700">Archived Stocks in <span id="archived-product-name" class="font-semibold text-red-500"></span></p>
          <button id="closeviewarchivedstocksmodal" class="p-2 rounded-full hover:bg-gray-100">
            <i class="fa-regular fa-xmark text-xl"></i>
          </button>
        </div>
        <div class="overflow-x-auto h-fit max-h-[70vh]">
          <table class="w-full text-sm">
            <thead class="bg-gray-200 text-gray-700 uppercase text-xs sticky top-0">
              <tr>
                <th class="p-3 text-left">#</th>
                <th class="p-3 text-left">Batch Number</th>
                <th class="p-3 text-left">Quantity</th>
                <th class="p-3 text-center">Expiry Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="archived-stocks-tbody">
              @if ($archivedstocks->isEmpty())
                <tr>
                  <td colspan="4" class="p-3 text-center text-red-500">No Archived Stocks Available</td>
                </tr>
              @else
                @foreach ($archivedstocks as $archivedstock)
                <tr data-product-id="{{ $archivedstock->product_id }}" data-batch="{{ $archivedstock->batch_number }}" data-quantity="{{ $archivedstock->quantity }}" data-expiry="{{ $archivedstock->expiry_date }}" class="hover:bg-gray-50">
                  <td class="text-left p-3">{{ $loop->iteration }}</td>
                  <td class="text-left font-semibold text-gray-700">{{ $archivedstock->batch_number }}</td>
                  <td class="text-left font-semibold text-gray-500">{{ $archivedstock->quantity }}</td>
                  <td class="text-center font-semibold text-gray-500">{{ Carbon::parse($archivedstock->expiry_date)->format('M d, Y') }}</td>
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
      </div>
  </div>