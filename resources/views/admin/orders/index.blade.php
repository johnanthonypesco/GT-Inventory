<x-app-layout>
    <x-admin.sidebar/>
    <div id="content-wrapper" class="transition-all duration-300 lg:ml-64 md:ml-20">
        <x-admin.header/>
        <main id="main-content" class="pt-20 p-4 lg:p-8 min-h-screen">
            
            {{-- Header Section --}}
            <div class="mb-6 pt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-20">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Order Management</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Home / <span class="text-red-700 dark:text-red-300 font-medium">Replenish Orders</span>
                    </p>
                </div>

                {{-- Create Button: Only for Pharmacists (Level 2) or Super Admin (Level 1) --}}
                @if(in_array(auth()->user()->user_level_id, [1, 2]))
                    <a href="{{ route('admin.orders.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white rounded-lg shadow-md transition-all duration-200">
                        <i class="fa-solid fa-plus mr-2"></i> Create New Order
                    </a>
                @endif
            </div>

            {{-- Alerts --}}
            @if (session('success'))
                <div id="successAlert" class="mb-4 flex items-center p-4 text-green-800 border-l-4 border-green-600 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
                    <i class="fa-solid fa-circle-check text-xl mr-3"></i>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif
            @if (session('error'))
                <div id="errorAlert" class="mb-4 flex items-center p-4 text-red-800 border-l-4 border-red-600 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800" role="alert">
                    <i class="fa-solid fa-circle-exclamation text-xl mr-3"></i>
                    <div class="text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif

            {{-- Orders Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4 font-medium">Order ID</th>
                                <th class="px-6 py-4 font-medium">Branch</th>
                                <th class="px-6 py-4 font-medium">Requester</th>
                                <th class="px-6 py-4 font-medium">Order Details (Items)</th>
                                <th class="px-6 py-4 font-medium">Status</th>
                                <th class="px-6 py-4 font-medium text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors duration-150">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                        #{{ $order->id }}
                                        <div class="text-xs font-normal text-gray-500">{{ $order->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $order->branch->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $order->user->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{-- Collapsible Details to see items without a modal --}}
                                        <details class="group cursor-pointer">
                                            <summary class="list-none text-blue-600 hover:text-blue-800 font-medium text-xs flex items-center">
                                                <span>View {{ $order->items->count() }} Items</span>
                                                <i class="fa-solid fa-chevron-down ml-1 text-[10px] transition-transform group-open:rotate-180"></i>
                                            </summary>
                                            <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700 text-xs">
                                                <ul class="space-y-1">
                                                    @foreach($order->items as $item)
                                                        <li class="flex justify-between">
                                                            <span>{{ $item->product->generic_name }}</span>
                                                            <span class="font-bold">x{{ $item->quantity_requested }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                @if($order->remarks)
                                                    <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700 italic text-gray-500">
                                                        "{{ $order->remarks }}"
                                                    </div>
                                                @endif
                                            </div>
                                        </details>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($order->status == 'pending_admin')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                <span class="w-2 h-2 mr-1 bg-yellow-500 rounded-full animate-pulse"></span>
                                                Waiting Admin
                                            </span>
                                        @elseif($order->status == 'pending_finance')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                <span class="w-2 h-2 mr-1 bg-blue-500 rounded-full animate-pulse"></span>
                                                Waiting Finance
                                            </span>
                                        @elseif($order->status == 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                <i class="fa-solid fa-check mr-1"></i> Approved
                                            </span>
                                        @elseif($order->status == 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                <i class="fa-solid fa-xmark mr-1"></i> Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            
                                            {{-- ============================== --}}
                                            {{-- SUPER ADMIN ACTIONS (Level 1) --}}
                                            {{-- ============================== --}}
                                            @if(Auth::user()->user_level_id == 1 && $order->status == 'pending_admin')
                                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Approve this order and send to Finance?');">
                                                    @csrf
                                                    <button name="action" value="approve" title="Approve" class="p-2 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 transition">
                                                        <i class="fa-solid fa-thumbs-up"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Reject this order?');">
                                                    @csrf
                                                    <button name="action" value="reject" title="Reject" class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition">
                                                        <i class="fa-solid fa-thumbs-down"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- ============================== --}}
                                            {{-- FINANCE ACTIONS (Level 6)      --}}
                                            {{-- ============================== --}}
                                            @if(Auth::user()->user_level_id == 6 && $order->status == 'pending_finance')
                                                 <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Grant Final Approval for this order?');">
                                                    @csrf
                                                    <button name="action" value="approve" title="Final Approve" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-xs font-medium transition shadow-sm">
                                                        Final Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Reject this order?');">
                                                    @csrf
                                                    <button name="action" value="reject" title="Reject" class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- ============================== --}}
                                            {{-- PRINT ACTION (For All)         --}}
                                            {{-- ============================== --}}
                                            @if($order->status == 'approved')
                                                <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank" class="p-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-red-700 transition" title="Print PDF">
                                                    <i class="fa-solid fa-print"></i>
                                                </a>
                                            @endif
                                            
                                            {{-- No Actions Placeholder --}}
                                            @if($order->status == 'rejected' || ($order->status == 'approved' && Auth::user()->user_level_id != 1 && Auth::user()->user_level_id != 6 && Auth::user()->user_level_id != 2))
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fa-regular fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                            <p>No orders found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $orders->links() }}
                </div>
            </div>

        </main>
    </div>

    {{-- Auto-hide alerts script --}}
    <script>
        setTimeout(() => {
            const success = document.getElementById('successAlert');
            const error = document.getElementById('errorAlert');
            if(success) success.remove();
            if(error) error.remove();
        }, 4000);
    </script>
</x-app-layout>