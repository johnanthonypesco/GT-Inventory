<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <title>Manage Blocked IPs</title>
</head>
<body class="flex flex-col md:flex-row m-0 p-0">
    <x-admin.navbar/>

    <main class="md:w-full lg:ml-[16%] opacity-0 px-4">
        <x-admin.header title="Manage Blocked IPs" icon="fa-solid fa-ban"/>
        
        <div class="flex flex-col md:flex-row justify-between items-center mt-24">
            <x-input id="search" name="search" class="w-full md:w-[40%] relative bg-white rounded-lg" type="text" placeholder="Search by IP address or reason..." classname="fa fa-magnifying-glass"/>
            
            <button onclick="history.back()" class="w-full md:w-fit mt-3 md:mt-0 p-2 bg-white rounded-lg flex items-center justify-center gap-2" style="box-shadow: 0 0 2px #003582;">
                <i class="fa-solid fa-arrow-left"></i> Go Back
            </button>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 my-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        
        <div class="p-2 bg-white rounded-md mt-5" style="box-shadow: 0 5px 8px rgba(0, 0, 0, 0.389)">
            <div id="blocked-ips-data-container">
                @include('admin.partials.blocked_ips_table')
            </div>
        </div>
    </main>

    <x-loader />
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search');
            const dataContainer = document.getElementById('blocked-ips-data-container');
            const baseUrl = "{{ route('blocked-ips.search') }}";

            const fetchData = async (url) => {
                try {
                    dataContainer.style.opacity = '0.5';
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const html = await response.text();
                    dataContainer.innerHTML = html;
                } catch (error) {
                    console.error('Failed to fetch data:', error);
                    dataContainer.innerHTML = '<p class="text-center text-red-500 p-4">Could not load data.</p>';
                } finally {
                    dataContainer.style.opacity = '1';
                }
            };

            const updateTable = () => {
                const params = new URLSearchParams({ search: searchInput.value });
                fetchData(`${baseUrl}?${params.toString()}`);
            };

            const debounce = (func, delay) => {
                let timeoutId;
                return (...args) => {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        func.apply(this, args);
                    }, delay);
                };
            };

            searchInput.addEventListener('keyup', debounce(updateTable, 300));

            dataContainer.addEventListener('click', function (e) {
                const targetLink = e.target.closest('a');
                if (targetLink && targetLink.href && targetLink.closest('.pagination')) {
                    e.preventDefault();
                    fetchData(targetLink.href);
                }
            });
        });
    </script>
</body>
</html>