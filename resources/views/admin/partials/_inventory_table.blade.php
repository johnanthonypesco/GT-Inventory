{{-- Ang file na ito ay naglalaman LAMANG ng table at pagination --}}
<table class="w-full">
    <thead class="sticky top-0 bg-gray-200">
        <tr>
            <th class="p-3 text-gray-700 uppercase text-sm text-left tracking-wide">#</th>
            <th class="p-3 text-gray-700 uppercase text-sm text-left tracking-wide">Batch Number</th>
            <th class="p-3 text-gray-700 uppercase text-sm text-left tracking-wide">Product Details</th>
            <th class="p-3 text-gray-700 uppercase text-sm text-left tracking-wide">Quantity</th>
            <th class="p-3 text-gray-700 uppercase text-sm tracking-wide">Status</th>
            <th class="p-3 text-gray-700 uppercase text-sm tracking-wide">Expiry Date</th>
            <th class="p-3 text-gray-700 uppercase text-sm text-left tracking-wide">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @if ($inventories->isEmpty())
            <tr>
                <td colspan="7" class="p-3 text-center text-sm text-gray-500">No inventory records available</td>
            </tr>
        @else
            @foreach ($inventories as $inventory)
            <tr data-stock-id="{{ $inventory->id }}"
                data-batch="{{ $inventory->batch_number }}"
                data-brand="{{ $inventory->product->brand_name }}"
                data-product="{{ $inventory->product->generic_name }}"
                data-form="{{ $inventory->product->form }}"
                data-strength="{{ $inventory->product->strength }}"
                data-quantity="{{ $inventory->quantity }}"
                data-expiry="{{ $inventory->expiry_date }}">
                <td class="p-3 text-sm text-gray-700 text-left">
                    {{ $loop->iteration + ($inventories->currentPage() - 1) * $inventories->perPage() }}
                </td>
                <td class="p-3 text-sm text-gray-700 text-left font-semibold">{{ $inventory->batch_number }}</td>
                <td class="p-3 text-sm text-gray-700 text-left">
                    <div class="flex gap-4">
                        <div>
                            <p class="font-semibold text-gray-700">{{ $inventory->product->generic_name }}</p>
                            <p class="italic text-gray-500">{{ $inventory->product->brand_name }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700">{{ $inventory->product->strength }}</p>
                            <p class="italic text-gray-500">{{ $inventory->product->form }}</p>
                        </div>
                    </div>
                </td>
                <td class="p-3 text-sm text-left font-semibold {{ $inventory->quantity < 100 ? 'text-yellow-700' : ($inventory->quantity == 0 ? 'text-red-700' : 'text-green-700') }}">
                    {{ $inventory->quantity }}
                </td>   
                <td class="p-3 text-sm text-gray-700">
                    @if ($inventory->quantity < 100 && $inventory->quantity > 0)
                        <p class="p-2 border-l-2 border-yellow-500 bg-yellow-50 text-yellow-700 font-semibold text-center">Low Stock</p>
                    @elseif ($inventory->quantity == 0)
                        <p class="p-2 border-l-2 border-red-500 bg-red-50 text-red-700 font-semibold text-center">Out of Stock</p>
                    @else
                        <p class="p-2 border-l-2 border-green-500 bg-green-50 text-green-700 font-semibold text-center">In Stock</p>
                    @endif 
                </td>
                <td class="p-3 text-sm text-gray-700 text-center font-semibold">
                    {{ \Carbon\Carbon::parse($inventory->expiry_date)->format('M d, Y') }}
                </td>
                <td class="p-3">
                    <button class="edit-stock-btn bg-blue-100 text-blue-700 p-2 rounded-lg hover:-translate-y-1 hover:shadow-md transition-all duration-200 hover:bg-blue-600 hover:text-white font-semibold text-sm">
                        <i class="fa-regular fa-pen-to-square mr-1"></i>
                        Edit Stock
                    </button>
                </td>
            </tr> 
            @endforeach
        @endif
    </tbody>
</table>

<div class="p-4 border-t bg-white flex flex-col sm:flex-row justify-between items-center gap-4">
    <p class="text-sm text-gray-600">
        Showing {{ $inventories->firstItem() ?? 0 }} to {{ $inventories->lastItem() ?? 0 }} of {{ $inventories->total() }} results
    </p>
    {{-- Nagdagdag ako ng class="pagination-links" dito para sa JavaScript --}}
    <div class="flex space-x-2 pagination-links">
        @if ($inventories->onFirstPage())
            <span class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">Previous</span>
        @else
            <a href="{{ $inventories->previousPageUrl() }}" class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100">Previous</a>
        @endif

        @foreach ($inventories->getUrlRange(1, $inventories->lastPage()) as $page => $url)
            @if ($page == $inventories->currentPage())
                <span class="px-3 py-2 text-sm bg-red-700 text-white rounded-lg">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100">{{ $page }}</a>
            @endif
        @endforeach

        @if ($inventories->hasMorePages())
            <a href="{{ $inventories->nextPageUrl() }}" class="px-3 py-2 text-sm bg-white135 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100">Next</a>
        @else
            <span class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">Next</span>
        @endif
    </div>
</div>


{{-- dito kona lagay script ng nasa inventory.blade.php para hindi kalat --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Hanapin ang container ng table
    const tableContainer = document.getElementById('inventory-data-container');

    // Gumamit ng 'event delegation' para pakinggan ang clicks sa pagination links
    tableContainer.addEventListener('click', function (event) {
        
        // Tignan kung ang pinindot ay isang <a> tag sa loob ng .pagination-links
        if (event.target.tagName === 'A' && event.target.closest('.pagination-links')) {
            
            // Pigilan ang default behavior (full page reload)
            event.preventDefault();

            let url = event.target.href;

            // Gumawa ng AJAX request (gamit ang Fetch API)
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Ito ang nagsasabi sa Laravel na AJAX request ito
                }
            })
            .then(response => response.text())
            .then(html => {
                // Palitan ang laman ng container ng bagong HTML galing sa server
                tableContainer.innerHTML = html;

                // I-update ang URL sa browser
                window.history.pushState({path: url}, '', url);

                // MAHALAGA: Kung ang iyong 'Edit Stock' button ay may JavaScript,
                // kailangan mong i-re-attach ang event listeners dito dahil bago na ang HTML.
                // Halimbawa: initEditStockButtons(); 
            })
            .catch(error => console.error('Error fetching pagination data:', error));
        }
    });

    // Handle ang paggamit ng Back/Forward button ng browser
    window.addEventListener('popstate', function () {
        fetch(location.href, { // location.href ay ang URL na pinuntahan (mula sa history)
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
            // I-re-attach din ang listeners dito
        })
        .catch(error => console.error('Error fetching popstate data:', error));
    });
});
</script>