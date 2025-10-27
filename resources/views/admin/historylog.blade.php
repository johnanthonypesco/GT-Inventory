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
                <p class="text-sm text-gray-600">
                    Home / <span class="text-red-700 font-medium">History Logs</span>
                </p>
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
                        <!-- Search Icon -->
                        <i class="fa-regular fa-magnifying-glass absolute left-3 top-[21%] transform text-gray-400 text-sm"></i>
                        
                        <!-- Search Input -->
                        <input 
                            id="searchInput"
                            type="text" 
                            placeholder="Search activity..." 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm transition-all"
                        >
                        
                        <!-- Tip Indicator -->
                        <p class="text-xs text-gray-400 mt-1 pl-1 italic">
                            Tip: You can search by 
                            <span class="font-medium text-gray-500">action</span>, 
                            <span class="font-medium text-gray-500">user</span>, 
                            <span class="font-medium text-gray-500">details</span>, or 
                            <span class="font-medium text-gray-500">date & time</span>.
                        </p>
                    </div>
                </div>

                <!-- Table wrapper (static container) -->
                <div class="relative overflow-x-auto p-5">
                    <!-- Loader Overlay (stays fixed here, not replaced) -->
                    <div id="table-loader" class="absolute inset-0 flex items-center justify-center bg-white/70 backdrop-blur-sm hidden z-10">
                        <div class="w-10 h-10 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                    </div>

                    <!-- Dynamic table content -->
                    <div id="history-table">
                        @include('admin.partials._history_table')
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

    <script src="{{ asset('js/historyLog.js') }}"></script>
</body>
</x-app-layout>
