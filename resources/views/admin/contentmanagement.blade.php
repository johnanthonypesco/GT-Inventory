@php
    use Carbon\Carbon;
@endphp

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
    <script src="https://cdn.tailwindcss.com"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Manage Content</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full lg:ml-[16%]">
        <x-admin.header title="Manage Content" icon="fa-solid fa-file" name="John Anthony Pesco" gmail="admin@gmail"/>

        <div class="table-container p-4 bg-white shadow-md rounded-md mt-5">
            <h1 class="text-2xl font-bold text-[#005382] mb-4">About us Content</h1>
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>About Us 1</th>
                            <th>About Us 2</th>
                            <th>About Us 3</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($content as $contentItem )
                        <tr>
                            <td class="text-left text-[10px]">{{ $contentItem->aboutus1 }}</td>
                            <td class="text-left text-[10px]">{{ $contentItem->aboutus2 }}</td>
                            <td class="text-left text-[10px]">{{ $contentItem->aboutus3 }}</td>
                            <td class="text-left text-[10px]">{{ $contentItem->contact_number }}</td>
                            <td class="text-left text-[10px]">{{ $contentItem->email }}</td>
                            <td class="text-left text-[10px]">{{ $contentItem->address }}</td>
                            <td>
                                <button 
                                    class="edit-btn flex items-center justify-center text-[#005382] cursor-pointer gap-2"
                                    data-id="{{ $contentItem->id }}"
                                    data-aboutus1="{{ $contentItem->aboutus1 }}"
                                    data-aboutus2="{{ $contentItem->aboutus2 }}"
                                    data-aboutus3="{{ $contentItem->aboutus3 }}"
                                    data-contact_number="{{ $contentItem->contact_number }}"
                                    data-email="{{ $contentItem->email }}"
                                    data-address="{{ $contentItem->address }}"
                                >
                                <i class="fa-regular fa-pen-to-square"></i>Edit</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>     
        </div>

        <div id="editmodal" class="hidden fixed bg-black/70 w-full h-full top-0 left-0 overflow-auto z-50">
            <div class="modal bg-white w-[90%] md:w-[80%] lg:w-[60%] mx-auto mt-10 p-5 rounded-md relative">
                <x-modalclose click="closeeditmodal"/>
                <h1 class="text-left text-[#005382] text-2xl font-bold mb-5">Edit Content</h1>
                
                <form id="editForm" method="POST" action=""> 
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="aboutus1" class="block text-gray-700 text-md font-bold mb-2">About Us 1:</label>
                        <textarea id="aboutus1" name="aboutus1" class="w-full p-2 border rounded-md @error('aboutus1') border-red-500 @else border-[#005382] @enderror" rows="3">{{ old('aboutus1') }}</textarea>
                        @error('aboutus1')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="aboutus2" class="block text-gray-700 text-md font-bold mb-2">About Us 2:</label>
                        <textarea id="aboutus2" name="aboutus2" class="w-full p-2 border rounded-md @error('aboutus2') border-red-500 @else border-[#005382] @enderror" rows="3">{{ old('aboutus2') }}</textarea>
                        @error('aboutus2')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
        
                    <div class="mb-4">
                        <label for="aboutus3" class="block text-gray-700 text-md font-bold mb-2">About Us 3:</label>
                        <textarea id="aboutus3" name="aboutus3" class="w-full p-2 border rounded-md @error('aboutus3') border-red-500 @else border-[#005382] @enderror" rows="3">{{ old('aboutus3') }}</textarea>
                        @error('aboutus3')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
        
                    <div class="mb-4">
                        <label for="contact_number" class="block text-gray-700 text-md font-bold mb-2">Phone Number:</label>
                        <input 
                            type="tel" 
                            id="contact_number" 
                            name="contact_number" 
                            value="{{ old('contact_number') }}" 
                            maxlength="11" 
                            placeholder="09xxxxxxxxx"
                            class="w-full p-2 border rounded-md @error('contact_number') border-red-500 @else border-[#005382] @enderror">
                        @error('contact_number')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
        
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-md font-bold mb-2">Email:</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full p-2 border rounded-md @error('email') border-red-500 @else border-[#005382] @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
        
                    <div class="mb-4">
                        <label for="address" class="block text-gray-700 text-md font-bold mb-2">Address:</label>
                        <textarea id="address" name="address" class="w-full p-2 border rounded-md @error('address') border-red-500 @else border-[#005382] @enderror" rows="3">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
        
                    <button type="button" id="updateButton" class="w-fit px-6 py-4 bg-[#005382] text-white rounded-lg hover:bg-[#00456a] transition-colors">Update Content</button>
                </form>
            </div>
        </div>

        <div class="table-container bg-white shadow-md rounded-md mt-5 p-4">
            <h1 class="text-2xl font-bold text-[#005382] mb-4">Shown Product in Promotional Page</h1>
            <div class="h-[34vh] overflow-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Generic Name</th>
                            <th class="hidden lg:table-cell">Brand Name</th>
                            <th class="hidden lg:table-cell">Form</th>
                            <th class="hidden lg:table-cell">Strength</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product as $productItem)
                            <tr>
                                <td>{{ $productItem->generic_name }}</td>
                                <td class="hidden lg:table-cell">{{ $productItem->brand_name }}</td>
                                <td class="hidden lg:table-cell">{{ $productItem->form }}</td>
                                <td class="hidden lg:table-cell">{{ $productItem->strength }}</td>
                                <td>
                                    <form action="{{ route('admin.product.enabledisable', $productItem->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button 
                                            type="submit" class="{{ $productItem->is_displayed ? 'bg-[#005382]' : 'bg-red-500' }} text-white px-4 py-2 rounded">{{ $productItem->is_displayed ? 'Enabled' : 'Disabled' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- loader --}}
        <x-loader />
        {{-- loader --}}
        <x-successmessage />
    </main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editmodal');
        const editForm = document.getElementById('editForm');
        const updateButton = document.getElementById('updateButton');
        
        // Function to open the modal and populate it with the correct data
        window.openeditmodal = function(button) {
            const id = button.dataset.id;
            // Create the correct route for the form action dynamically
            const actionTemplate = "{{ route('admin.contentmanagement.edit', ['id' => 'ID_PLACEHOLDER']) }}";
            const actionUrl = actionTemplate.replace('ID_PLACEHOLDER', id);
            
            editForm.action = actionUrl;
            
            // Populate form fields from the button's data attributes
            document.getElementById('aboutus1').value = button.dataset.aboutus1;
            document.getElementById('aboutus2').value = button.dataset.aboutus2;
            document.getElementById('aboutus3').value = button.dataset.aboutus3;
            document.getElementById('contact_number').value = button.dataset.contact_number;
            document.getElementById('email').value = button.dataset.email;
            document.getElementById('address').value = button.dataset.address;

            editModal.classList.remove('hidden');
        }

        // Function to close the modal (used by your x-modalclose component)
        window.closeeditmodal = function() {
            document.getElementById('editmodal').classList.add('hidden');
        }
        
        // Re-open the modal automatically if the page was reloaded with validation errors
        @if($errors->any())
            editModal.classList.remove('hidden');
        @endif

        // Attach event listeners to all edit buttons
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                openeditmodal(this);
            });
        });
    });
    
</script>
<script>window.successMessage = @json(session('success'));</script>
<script src="{{ asset('js/sweetalert/managecontentsweetalert.js') }}"></script>
</body>
</html>