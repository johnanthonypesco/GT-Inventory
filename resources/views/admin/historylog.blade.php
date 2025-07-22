
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>History Log</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full lg:ml-[16%]">
        <x-admin.header title="History Log" icon="fa-solid fa-history"/>
        
        <div class="p-4 bg-white rounded-md mt-5">
            <div class="flex flex-col md:flex-row justify-between">
                <x-input id="search" class="w-full md:w-[40%] relative" type="text" placeholder="Search History Log by Event..." classname="fa fa-magnifying-glass"/>
                <select id="eventFilter" class="p-2 cursor-pointer rounded-lg mt-3 md:mt-0 w-full md:w-fit bg-white outline-none" style="box-shadow: 0 0 2px #003582;">
                    <option value="All">--All Events--</option>
                    <option value="Add">Add</option>
                    <option value="Edit">Edit</option>
                    <option value="Delete">Delete</option>
                </select>
            </div>

            <div class="overflow-x-auto mt-5 h-[60vh]">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Event</th>
                            <th>Description</th>
                            <th>Action By</th>
                        </tr>
                    </thead>
                    <tbody id="logTableBody">
                        @if($historylogs->isEmpty())
                            <tr id="noDataRow">
                                <td colspan="4" class="text-center p-4">No history logs available.</td>
                            </tr>
                        @else
                            @foreach($historylogs as $log)
                                <tr data-event="{{ $log->event }}">
                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('F d, Y') }} <span class="font-light ml-2">{{ \Carbon\Carbon::parse($log->created_at)->format('h:i A') }}</span></td>
                                    <td class="flex justify-center">
                                        <p class="py-1 px-3 text-white rounded-md w-20 text-center text-[12px]
                                            {{ $log->event == 'Add' ? 'bg-blue-500/70' : ($log->event == 'Edit' ? 'bg-green-500/70' : ($log->event == 'Archive' ? 'bg-red-600/70' : 'bg-red-500/50')) }}">
                                            {{ $log->event }}
                                        </p>
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td>{{ $log->user_email ?? 'Unknown User' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <x-pagination 
                currentPage="{{ $currentPage }}" 
                totalPage="{{ $totalPage }}" 
                prev="{{ $prevPageUrl }}" 
                next="{{ $nextPageUrl }}" 
            />
        </div>
    </main>
    
</body>
<script>
    document.getElementById('eventFilter').addEventListener('change', function () {
        let selectedEvent = this.value;
        let rows = document.querySelectorAll('#logTableBody tr');
        let hasVisibleRow = false;

        rows.forEach(row => {
            let eventType = row.getAttribute('data-event');

            if (selectedEvent === "All" || eventType === selectedEvent) {
                row.style.display = "";
                hasVisibleRow = true;
            } else {
                row.style.display = "none";
            }
        });

        let noDataRow = document.getElementById('noDataRow');
        if (noDataRow) {
            noDataRow.remove();
        }

        if (!hasVisibleRow) {
            let tbody = document.getElementById('logTableBody');
            let noDataMessage = document.createElement('tr');
            noDataMessage.id = 'noDataRow';
            noDataMessage.innerHTML = `
                <td colspan="4" class="text-center p-4">No history logs available.</td>
            `;
            tbody.appendChild(noDataMessage);
        }
    });
</script>
</html>
