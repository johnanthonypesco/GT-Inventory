
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>History Log</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full md:ml-[16%]">
        <x-admin.header title="History Log" icon="fa-solid fa-history"/>
        
        <div class="p-4 bg-white rounded-md mt-5">
            <div class="flex flex-col md:flex-row justify-between">
                <x-input id="search" class="w-full md:w-[40%] relative" type="text" placeholder="Search History Log by Event..." classname="fa fa-magnifying-glass"/>
                <select class="p-2 cursor-pointer rounded-lg mt-3 md:mt-0 w-full md:w-fit bg-white outline-none" style="box-shadow: 0 0 2px #003582;">
                    <option value="All">--All Events from--</option>
                    <option value="">Inventory</option>
                    <option value="">Product Deals</option>
                    <option value="">Orders</option>
                    <option value="">Manage Account</option>
                </select>  
            </div>

            <div class="overflow-x-auto mt-5 h-[60vh]">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Event</th>
                            <th>Description</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historylogs as $log)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('F d, Y h:i:s A') }}</td>
                                <td class="flex justify-center">
                                    <p class="p-2 text-white rounded-md w-20 text-center 
                                        {{ $log->event == 'Add' ? 'bg-slate-500' : ($log->event == 'Edit' ? 'bg-green-500' : 'bg-red-500') }}">
                                        {{ $log->event }}
                                    </p>
                                </td>
                                <td>{{ $log->description }}</td>
                                <td>{{ $log->user_email ?? 'Unknown User' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center p-4">No history logs available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination/>
        </div>
    </main>
    
</body>
</html>
