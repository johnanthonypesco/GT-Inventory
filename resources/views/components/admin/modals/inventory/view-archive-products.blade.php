<div class="fixed w-full h-screen top-0 left-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden" id="viewarchiveproductsmodal">
      <div class="modal bg-white rounded-lg w-full max-w-4xl max-h-screen overflow-y-auto p-6">
        <div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-4 sticky top-0 bg-white z-10">
          <p class="text-xl font-medium text-gray-700">Archived Products</p>
          <button id="closeviewarchiveproductsmodal" class="p-2 rounded-full hover:bg-gray-100">
            <i class="fa-regular fa-xmark text-xl"></i>
          </button>
        </div>
        <div class="overflow-x-auto h-fit max-h-[70vh]">
          <table class="w-full text-sm text-left">
            <thead class="bg-gray-200 text-gray-700 uppercase text-xs sticky top-0">
              <tr>
                <th class="p-3">#</th>
                <th class="p-3">Product Details</th>
                <th class="p-3 text-center">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              @if ($archiveproducts->isEmpty())
                <tr>
                  <td colspan="3" class="p-3 text-center text-red-500">No Archived Products Available</td>
                </tr>
              @endif
              @foreach ($archiveproducts as $product)
              <tr class="hover:bg-gray-50"
                  data-product-id="{{ $product->id }}"
                  data-brand="{{ $product->brand_name }}"
                  data-product="{{ $product->generic_name }}"
                  data-form="{{ $product->form }}"
                  data-strength="{{ $product->strength }}">
                <td class="p-3">{{ $loop->iteration }}</td>
                <td class="p-3">
                  <div class="flex gap-4">
                    <div>
                      <p class="font-semibold text-gray-700">{{ $product->generic_name }}</p>
                      <p class="italic text-gray-500">{{ $product->brand_name }}</p>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-700">{{ $product->strength }}</p>
                      <p class="italic text-gray-500">{{ $product->form }}</p>
                    </div>
                  </div>
                </td>
                <td class="p-3 flex items-center justify-center gap-2 font-semibold">
                  <button class="view-archivestock-btn bg-blue-100 text-blue-700 p-2 rounded-lg hover:-translate-y-1 duration-300 hover:bg-blue-600 hover:text-white transition-all mr-2" data-product-id="{{ $product->id }}">
                    <i class="fa-regular fa-eye mr-1"></i>View Archived Stock
                  </button>
                  <form action="{{ route('admin.inventory.unarchiveproduct') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button class="restore-product-btn bg-green-100 text-green-700 p-2 rounded-lg hover:-translate-y-1 duration-300 hover:bg-green-600 hover:text-white transition-all">
                      <i class="fa-regular fa-rotate-left mr-1"></i>Restore
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
  </div>