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
            {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <title>History Log</title>
</head>
<body class="flex flex-col md:flex-row m-0 p-0">
    <x-admin.navbar/>

    <main class="md:w-full lg:ml-[15%] opacity-0 px-4">
        <x-admin.header title="History Log" icon="fa-solid fa-history"/>
        
        <div class="flex flex-col md:flex-row justify-between mt-24">
            {{-- Added name="search" for easier access in JS --}}
            <x-input id="search" name="search" class="w-full md:w-[40%] relative bg-white rounded-lg" type="text" placeholder="Search logs by description, event, or user..." classname="fa fa-magnifying-glass"/>
            
            {{-- Added name="event" and included all event types --}}
            <select id="eventFilter" name="event" class="p-2 cursor-pointer rounded-lg mt-3 md:mt-0 w-full md:w-fit bg-white outline-none" style="box-shadow: 0 0 2px #003582;">
                <option value="All">--All Events--</option>
                <option value="Add">Add</option>
                <option value="Edit">Edit</option>
                <option value="Delete">Delete</option>
                <option value="Restore">Restore</option>

                <option value="Archive">Archive</option>
                <option value="Approve">Approve</option>
                <option value="Disapprove">Disapprove</option>
                <option value="Login">Login</option>
                <option value="Logout">Logout</option>
                <option value="Failed Login">Failed Login</option>
            </select>
        </div>
        <div class="p-2 bg-white rounded-md mt-5" style="box-shadow: 0 5px 8px rgba(0, 0, 0, 0.389)">

            <div id="historylog-data-container">
                {{-- This includes the table partial for the initial page load. --}}
                {{-- It will be replaced with new content when a filter is used. --}}
                @include('admin.partials.historylog_table')
            </div>
        </div>
    </main>

{{-- loader --}}
<x-loader />
{{-- loader --}}
    
</body>

{{-- This new script replaces your old one. It handles all AJAX logic. --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search');
        const eventFilter = document.getElementById('eventFilter');
        const dataContainer = document.getElementById('historylog-data-container');

        // The base URL for our AJAX requests, pointing to the new route.
        const baseUrl = "{{ route('admin.historylog.search') }}";

        // Function to fetch data from the server without reloading the page.
        const fetchData = async (url) => {
            try {
                // Add a visual indicator to show the user something is happening.
                dataContainer.style.opacity = '0.5';
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const html = await response.text();
                // Replace the content of the container with the new table from the server.
                dataContainer.innerHTML = html;
            } catch (error) {
                console.error('Failed to fetch history logs:', error);
                dataContainer.innerHTML = '<p class="text-center text-red-500 p-4">Could not load data. Please try again.</p>';
            } finally {
                // Restore the opacity once the content is loaded.
                dataContainer.style.opacity = '1';
            }
        };

        // Function to build the correct URL with filter parameters and then call fetchData.
        const updateTable = () => {
            const search = searchInput.value;
            const event = eventFilter.value;
            
            // Use URLSearchParams to safely build the query string.
            const params = new URLSearchParams({
                search: search,
                event: event
            });

            fetchData(`${baseUrl}?${params.toString()}`);
        };

        // A "debounce" function to prevent sending too many requests while the user is typing.
        const debounce = (func, delay) => {
            let timeoutId;
            return (...args) => {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                }, delay);
            };
        };

        // --- EVENT LISTENERS ---

        // Listen for typing in the search bar.
        searchInput.addEventListener('keyup', debounce(updateTable, 300)); // 300ms delay

        // Listen for changes in the dropdown filter.
        eventFilter.addEventListener('change', updateTable);

        // Listen for clicks inside the data container to handle pagination links.
dataContainer.addEventListener('click', function (e) {
    // First, find the closest anchor tag to what was clicked.
    const targetLink = e.target.closest('a');

    // Then, check if that link exists and is inside a .pagination container.
    // Also, ensure it has an href to prevent errors.
    if (targetLink && targetLink.href && targetLink.closest('.pagination')) {
        e.preventDefault(); // Stop the browser from navigating to the new page.
        fetchData(targetLink.href); // Fetch the content for the clicked page instead.
    }
});
    });
</script>
</html>