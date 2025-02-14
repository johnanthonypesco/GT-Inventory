<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <title>Manage Account</title>
</head>
<body class="bg-[#BBBCBE] flex p-5 gap-5">
    <x-customer.navbar/>

    <main class="w-full">
        <x-customer.header title="Manage Account Page" icon="fa-solid fa-gear"/>
        
        <div class="mt-5">
            <div class="bg-white p-5 relative flex flex-col justify-center gap-5 rounded-xl">
                <div class="absolute top-0 left-0 h-[30%] bg-[#005382] w-full z-1" style="border-radius: 10px 10px 0 0 "></div>
                <i class="fa-solid fa-user text-[#005382] border w-fit text-4xl bg-white p-5 rounded-full z-2"></i>
                <p class="text-xl font-semibold">Wesleyan Hospital</p>
                <button onclick="editaccount()" class="bg-white text-black w-fit px-3 py-2 rounded-lg flex items-center gap-2" style="box-shadow: 0 0 5px #00528288"><i class="fa-solid fa-pen"></i>Edit Profile</button>
            </div>
            <div class="bg-white mt-5 p-5 rounded-xl">
                <p class="text-xl font-semibold">Account Information</p>

                <x-label-input label="Account Name" type="text" for="accountname" value="Wesleyan Hospital" divclass="mt-5" disabled/>
                <x-label-input label="Account Username" type="text" for="username" value="jewelmatapang" divclass="mt-5" disabled/>
                <x-label-input label="Account Password" type="password" id="password" for="password" value="jewelmatapang" divclass="mt-5 relative">
                    <x-view-password onclick="password()"/>
                </x-label-input>

            </div>
        </div>

        {{-- Modal for edit account --}}
        <div class="fixed hidden top-0 left-0 w-full h-full bg-black/50 z-10 p-5 pt-20" id="editaccount">
            <div class="modal bg-white p-5 rounded-lg w-[80%] lg:w-[40%] m-auto relative">
                <span onclick="closeeditaccount()" class="cursor-pointer absolute -top-10 -right-3 text-red-600 font-bold text-[50px]">&times;</span>
                <p class="text-xl font-semibold text-center text-[#005382]">Edit Account</p>
                {{-- Form --}}
                <form action="">
                    <x-label-input label="Account Name" type="text" for="accountname" value="Wesleyan Hospital" divclass="mt-5" disabled/>
                    <x-label-input label="Account Username" type="text" for="username" value="jewelmatapang" divclass="mt-5"/>
                    <x-label-input label="Account Password" type="password" id="modalpassword" for="password" value="jewelmatapang" divclass="mt-5 relative">
                        <x-view-password onclick="modalpassword()"/>
                    </x-label-input>

                    <x-submit-button/>
                </form>
                {{-- Form --}}
            </div>
        </div>
        {{-- Modal for edit account --}}
    </main>
</body>
</html>

<script src="{{ asset('js/customeraccount.js') }}"></script>
