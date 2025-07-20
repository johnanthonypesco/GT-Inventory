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
                            <button class="flex items-center justify-center text-[#005382] cursor-pointer gap-2"><i class="fa-regular fa-pen-to-square"></i>Edit</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </main>

</body>
</html>
