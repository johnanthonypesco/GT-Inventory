<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
            {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <title>Review Manager</title>
</head>
<body class="flex flex-col md:flex-row m-0 p-0">
    <x-admin.navbar/>

    <main class="md:w-full h-full lg:ml-[15%] opacity-0 px-4">
        <x-admin.header title="Manage Reviews" icon="fa-solid fa-star" name="John Anthony Pesco" gmail="admin@gmail"/>

        <div class="w-full mt-24 bg-white p-5 rounded-lg" style="box-shadow: 0 5px 8px rgba(0, 0, 0, 0.389)">
            <h1 class="font-bold text-2xl text-[#005382] mb-4">Customer Reviews</h1>
            
            <div class="overflow-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Company Name</th>

                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Public</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        <tr>
                            <td>{{ $review->user->name }}</td>
                            <td>{{ $review->user->company->name }}</td>

                            <td>{{ $review->rating }}</td>
                            <td>{{ $review->comment }}</td>
                            <td>{{ $review->allow_public_display ? 'Yes' : 'No' }}</td>
                            <td class="font-bold">
                                @if ($review->is_approved)
                                    <span class="text-green-600 font-semibold">Approved</span>
                                @else
                                    <span class="text-yellow-600 font-semibold">Pending</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ $review->is_approved 
                                    ? route('superadmin.reviews.disapprove', $review) 
                                    : route('superadmin.reviews.approve', $review) }}" id="approve-form" method="POST">
                                    @csrf
                                    <button type="button" id="approve-button" class="px-4 py-2 rounded text-white font-bold
                                        {{ $review->is_approved ? 'bg-red-600/30 text-red-600 hover:text-white hover:bg-red-600 hover:-translate-y-1 transition-all duration-200' : 'bg-blue-600/30 text-blue-600 hover:text-white hover:bg-blue-600 hover:-translate-y-1 transition-all duration-200' }}">
                                        {{ $review->is_approved ? 'Disapprove' : 'Approve' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-600 p-4">No reviews found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>
    
    {{-- loader --}}
    <x-loader />
    {{-- loader --}}
    <x-successmessage />

    <script src="{{ asset('js/sweetalert/reviewmanagersweetalert.js') }}"></script>
    <script>window.successMessage = @json(session('success'));</script>
    <script>window.errorMessage = @json(session('error'));</script>
    
</body>
</html>
