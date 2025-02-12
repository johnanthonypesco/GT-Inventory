<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <link rel="stylesheet" href="{{asset ('css/manageaccount.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Manage Account</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-[82%] md:w-full">
        <x-admin.header title="Manage Account Page" icon="fa-solid fa-bars-progress" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- Filter --}}
        <div class="flex items-center justify-end gap-2">
            <select name="account" id="account" class="w-full md:text-[20px] text-[15px] h-fit w-fit md:w-fit shadow-sm shadow-blue-500 p-2 rounded-lg mt-5 md:mt-9 h-10 text-center text-black font-semibold bg-white outline-none">
                <option value="account">All Account</option>
                <option value="account">Staff</option>
                <option value="account">Customer</option>    
            </select>

            <button onclick="addaccount()" class="w-full md:text-[20px] h-fit text-[15px] font-semibold text-black md:w-fit bg-white shadow-blue-500 shadow-sm p-[5px] rounded-lg mt-5 md:mt-9 flex items-center justify-center gap-2 hover:cursor-pointer"><i class="fa-solid fa-plus"></i>Add Account</button>
        </div>
        {{-- Filter --}}

        <div class="w-full bg-white h-[490px] mt-3 rounded-lg p-5">
            <div class="flex justify-between items-center flex-col md:flex-row gap-2">
                <h1 class="font-bold text-3xl text-[#005382]">Account List</h1>
                {{-- Search --}}
                <x-input name="search" placeholder="Search Customer by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>
                {{-- Search --}}
            </div>

            {{-- Table for Customer List --}}
            <div class="table-container mt-5 overflow-auto md:h-[80%]">
                <table>
                    <thead>
                        <tr>
                            <th>Account Id</th>
                            <th>Customer Name</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1234</td>
                            <td>Jewel Velasquez</td>
                            <td>jewelvelasquez</td>
                            <td>******</td>
                            {{-- Action --}}
                            <td class="flex justify-center items-center gap-4">
                                <button class="flex items-center text-[#005382] cursor-pointer" onclick="editaccount()"><i class="fa-regular fa-pen-to-square mr-2"></i>Edit</button>
                                <button class="flex items-center text-red-500 cursor-pointer" onclick="return confirm('Are you sure you want to delete')"><i class="fa-solid fa-trash mr-2"></i>Delete</button>
                            </td>
                            {{-- Action --}}
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Table for Customer List --}}
        </div>

        {{-- Modal for Add Account --}}
        <div class="w-full hidden bg-black/60 h-full fixed top-0 left-0 p-10 md:p-20" id="addaccount">
            <div class="modal w-full md:w-[40%] h-fit bg-white m-auto rounded-lg relative p-10">
                <span onclick="closeaddaccount()" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">&times;</span>
                <form action="">
                    <h1 class="text-3xl text-[#005382] font-bold text-center">Add New Account</h1>

                    <div>
                        <label for="name" class="text-black/80 font-semibold text-lg tracking-wide">Account Name:</label>
                        <input type="text" name="name" placeholder="Enter Account Name" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-1">
                    </div>

                    <div class="mt-2">
                        <label for="username" class="text-black/80 font-semibold text-lg tracking-wide">Username:</label>
                        <input type="text" name="username" placeholder="Enter Username" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-1">
                    </div>

                    <div class="mt-2">
                        <label for="password" class="text-black/80 font-semibold text-lg tracking-wide">Account Password:</label>
                        <input type="password" name="password" placeholder="Enter Account Password" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-1">
                    </div>
                    
                    <button type="submit" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer"><img src="{{asset('image/image 51.png')}}">Submit</button>
                </form>
            </div>
        </div>
        {{-- Modal for Add Account --}}

        {{-- Modal for Edit Account --}}
        <div class="w-full hidden bg-black/60 h-full fixed top-0 left-0 p-10 md:p-20" id="editaccount">
            <div class="modal w-full md:w-[40%] h-fit bg-white m-auto rounded-lg relative p-10">
                <span onclick="closeeditaccount()" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">&times;</span>
                <form action="">
                    <h1 class="text-3xl text-[#005382] font-bold text-center">Edit Account</h1>

                    <div>
                        <label for="name" class="text-black/80 font-semibold text-lg tracking-wide">Account Name:</label>
                        <input type="text" name="name" placeholder="Enter Account Name" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-1" value="Jewel Velasquez">
                    </div>

                    <div class="mt-2">
                        <label for="username" class="text-black/80 font-semibold text-lg tracking-wide">Username:</label>
                        <input type="text" name="username" placeholder="Enter Username" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-1" value="jewelvelasquez">
                    </div>

                    <div class="mt-2">
                        <label for="password" class="text-black/80 font-semibold text-lg tracking-wide">Account Password:</label>
                        <input type="password" name="password" placeholder="Enter Account Password" class="w-full p-2 outline-none border border-[#005382] rounded-lg mt-1" value="******">
                    </div>

                    <button type="submit" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer"><img src="{{asset('image/image 51.png')}}">Save</button>
                </form>
            </div>
        </div>
        {{-- Modal for Edit Account --}}
    </main>
    
</body>

<script src="{{asset ('js/manageaccount.js')}}"></script>
</html>