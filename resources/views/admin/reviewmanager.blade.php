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
            @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Review Manager</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full lg:ml-[16%]">
        <x-admin.header title="Manage Reviews" icon="fa-solid fa-star" name="John Anthony Pesco" gmail="admin@gmail"/>

        <div class="w-full mt-5 bg-white p-5 rounded-lg">
            <h1 class="font-bold text-2xl text-[#005382] mb-4">Customer Reviews</h1>
            
            <div class="overflow-auto">
                <table class="w-full table-auto text-left border border-gray-200">
                    <thead class="bg-[#005382] text-white">
                        <tr>
                            <th class="p-2">Customer Name</th>
                            <th class="p-2">Company Name</th>

                            <th class="p-2">Rating</th>
                            <th class="p-2">Comment</th>
                            <th class="p-2">Public</th>
                            <th class="p-2">Status</th>
                            <th class="p-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        <tr class="border-t">
                            <td class="p-2">{{ $review->user->name }}</td>
                            <td class="p-2">{{ $review->user->company->name }}</td>

                            <td class="p-2">{{ $review->rating }}</td>
                            <td class="p-2">{{ $review->comment }}</td>
                            <td class="p-2">{{ $review->allow_public_display ? 'Yes' : 'No' }}</td>
                            <td class="p-2">
                                @if ($review->is_approved)
                                    <span class="text-green-600 font-semibold">Approved</span>
                                @else
                                    <span class="text-yellow-600 font-semibold">Pending</span>
                                @endif
                            </td>
                            <td class="p-2">
                                <form action="{{ $review->is_approved 
                                    ? route('superadmin.reviews.disapprove', $review) 
                                    : route('superadmin.reviews.approve', $review) }}" id="approve-form" method="POST">
                                    @csrf
                                    <button type="button" id="approve-button" class="px-4 py-2 rounded text-white
                                        {{ $review->is_approved ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }}">
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
