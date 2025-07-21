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
    <link rel="icon" href="{{ asset('image/Logowname.png') }}" type="image/png">
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Manage Content</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full md:ml-[16%]">
        <x-admin.header title="Manage Content" icon="fa-solid fa-file" name="John Anthony Pesco" gmail="admin@gmail"/>

        <div class="table-container p-4 bg-white shadow-md rounded-md mt-5">
        <div>
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
                    @foreach ($content as $content )            
                    <tr>
                        <td class="text-left text-[10px]">{{ $content->aboutus1 }}</td>
                        <td class="text-left text-[10px]">{{ $content->aboutus2 }}</td>
                        <td class="text-left text-[10px]">{{ $content->aboutus3 }}</td>
                        <td class="text-left text-[10px]">{{ $content->contact_number }}</td>
                        <td class="text-left text-[10px]">{{ $content->email }}</td>
                        <td class="text-left text-[10px]">{{ $content->address }}</td>
                        <td>
                            <button 
                                class="edit-btn flex items-center justify-center text-[#005382] cursor-pointer gap-2"
                                data-id="{{ $content->id }}"
                                data-aboutus1="{{ $content->aboutus1 }}"
                                data-aboutus2="{{ $content->aboutus2 }}"
                                data-aboutus3="{{ $content->aboutus3 }}"
                                data-contact_number="{{ $content->contact_number }}"
                                data-email="{{ $content->email }}"
                                data-address="{{ $content->address }}"
                            >
                            <i class="fa-regular fa-pen-to-square"></i>Edit</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div id="editmodal" class="fixed bg-black/70 w-full h-full top-0 left-0 overflow-auto">
            <div class="modal bg-white w-[90%] md:w-[80%] lg:w-[60%] mx-auto mt-10 p-5 rounded-md relative">
                <x-modalclose click="closeeditmodal"/>
                <h1 class="text-left text-[#005382] text-2xl font-bold mb-5">Edit Content</h1>
                <form id="editForm" method="POST" action="{{ route('admin.contentmanagement.edit', ['id' => $content->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="aboutus1" class="block text-gray-700 text-sm font-bold mb-2">About Us 1:</label>
                        <textarea id="aboutus1" name="aboutus1" class="w-full p-2 border border-[#005382] rounded-md" rows="3">{{ $content->aboutus1 }}</textarea>
                    </div>    
                    <div class="mb-4">
                        <label for="aboutus2" class="block text-gray-700 text-sm font-bold mb-2">About Us 2:</label>
                        <textarea id="aboutus2" name="aboutus2" class="w-full p-2 border border-[#005382] rounded-md" rows="3">{{ $content->aboutus2 }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label for="aboutus3" class="block text-gray-700 text-sm font-bold mb-2">About Us 3:</label>
                        <textarea id="aboutus3" name="aboutus3" class="w-full p-2 border border-[#005382] rounded-md" rows="3">{{ $content->aboutus3 }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label for="contact_number" class="block text-gray-700 text-sm font-bold mb-2">Phone Number:</label>
                        <input type="text" id="contact_number" name="contact_number" value="{{ $content->contact_number }}" class="w-full p-2 border border-[#005382] rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                        <input type="email" id="email" name="email" value="{{ $content->email }}" class="w-full p-2 border border-[#005382] rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="address" class="block text-gray-700 text-sm font-bold mb-2">Address:</label>
                        <textarea id="address" name="address" class="w-full p-2 border border-[#005382] rounded-md" rows="3">{{ $content->address }}</textarea>
                    </div>
                    <button type="button" id="updateButton" class="w-fit px-6 py-4 bg-[#005382] text-white rounded-lg">Update Content</button>
                </form>

            </div>
        </div>
    </div>
    </main>

</body>


</html>
<script src="{{ asset('js/managecontent.js') }}"></script>
