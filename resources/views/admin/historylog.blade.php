<x-app-layout>
    <x-slot name="title">
        HistoryLog - General Tinio
    </x-slot>
<body class="bg-gray-50">
 
    <x-admin.sidebar/>
 
    <div id="content-wrapper" class="transition-all duration-300 lg:ml-64 md:ml-20">
        <x-admin.header/>
        <main id="main-content" class="pt-20 p-4 lg:p-8 min-h-screen">
            <div class="mb-6 pt-16">
                <p class="text-sm text-gray-600">Home / <span class="text-red-700 font-medium">History Logs</span></p>
            </div>
 
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
                <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-lg font-semibold text-gray-700">System Activity Timeline</h2>
                    <div class="flex gap-2">
                        <button class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                            <i class="fas fa-filter text-xs"></i> Filter
                        </button>
                    </div>
                </div>

                <div class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="relative w-full md:w-1/2">
                        <i class="fa-regular fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" placeholder="Search logs..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm transition-all">
                    </div>
                </div>
               
                <div class="overflow-x-auto p-5">
                    <table class="min-w-full table-auto border-collapse">
                        <thead class="bg-gray-200 text-gray-700 sticky top-0">
                            <tr>
                                <th class="p-4 text-gray-600 uppercase text-xs font-bold text-left tracking-wider border-b border-gray-200">#</th>
                                <th class="p-4 text-gray-600 uppercase text-xs font-bold text-left tracking-wider border-b border-gray-200">Action</th>
                                <th class="p-4 text-gray-600 uppercase text-xs font-bold text-center tracking-wider border-b border-gray-200">User</th>
                                <th class="p-4 text-gray-600 uppercase text-xs font-bold text-left tracking-wider border-b border-gray-200">Details</th>
                                <th class="p-4 text-gray-600 uppercase text-xs font-bold text-left tracking-wider border-b border-gray-200">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($historyLogs as $index => $log)
                                <tr class="text-gray-700 hover:bg-gray-50 transition duration-100">
                                    <td class="p-4 text-sm font-medium">{{ ($historyLogs->currentPage() - 1) * $historyLogs->perPage() + $loop->iteration }}</td>
                                    <td class="p-4 text-sm">
                                        @php
                                            $badgeColor = match(strtoupper($log->action)) {
                                                'PRODUCT REGISTERED' => 'bg-green-100 text-green-800',
                                                'PRODUCT UPDATED' => 'bg-blue-100 text-blue-800',
                                                'PRODUCT ARCHIVED' => 'bg-red-100 text-red-800',
                                                'PRODUCT UNARCHIVED' => 'bg-yellow-100 text-yellow-800',

                                                'STOCK ADDED' => 'bg-green-100 text-green-800',
                                                'STOCK UPDATED' => 'bg-blue-100 text-blue-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }} text-center">
                                            {{ strtoupper($log->action) }}
                                        </span>
                                    </td>

                                    <td class="p-4 text-sm text-center font-medium">{{ $log->user_name ?? 'System' }}</td>

                                    <td class="p-4 text-sm text-gray-500">
                                        @php
                                            $maxLength = 100; // Adjust to your preferred cutoff length
                                            $isLong = strlen($log->description) > $maxLength;
                                            $shortText = $isLong ? substr($log->description, 0, $maxLength) . '...' : $log->description;
                                        @endphp

                                        <span class="log-description">{{ $shortText }}</span>
                                        
                                        @if($isLong)
                                            <button 
                                                class="text-blue-400 no-underline font-bold hover:underline text-sm ml-1 view-more-btn" 
                                                data-full="{{ e($log->description) }}"
                                            >
                                                View More
                                            </button>
                                        @endif
                                    </td>

                                    <td class="p-4 text-sm">{{ $log->created_at->format('F j, Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">No history logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
 
                <div class="p-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-gray-600">Showing {{ $historyLogs->firstItem() ?? 0 }} to {{ $historyLogs->lastItem() ?? 0 }} of {{ $historyLogs->total() }} results</p>
                    <div class="flex space-x-2">
                        {{ $historyLogs->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
 
    <!-- Modal -->
    <div id="viewMoreModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-11/12 md:w-1/2 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Full Description</h3>
            <p id="modalDescription" class="text-gray-600 whitespace-pre-line"></p>
            <div class="mt-4 text-right">
                <button id="closeModalBtn" class="px-4 py-2 bg-red-700 text-white text-sm rounded-lg hover:bg-red-800 font-semibold">Close</button>
            </div>
        </div>
    </div>

</body>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('viewMoreModal');
        const modalDesc = document.getElementById('modalDescription');
        const closeBtn = document.getElementById('closeModalBtn');

        document.querySelectorAll('.view-more-btn').forEach(button => {
            button.addEventListener('click', () => {
                modalDesc.textContent = button.dataset.full;
                modal.classList.remove('hidden');
            });
        });

        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
        });
    });
</script>
</x-app-layout>