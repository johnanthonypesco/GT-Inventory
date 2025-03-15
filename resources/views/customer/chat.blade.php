<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <title>Chat</title>
</head>

<body class="flex flex-col md:flex-row gap-4 h-[100vh] p-5">
    <x-customer.navbar />

    <main class="md:w-full h-full md:ml-[18%] ml-0">
        <x-customer.header title="Chat" icon="fa-solid fa-message"/>
        
        <x-input name="search" placeholder="Search Conversation by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative mt-5 rounded-lg"/>
        
        
        <div class="flex gap-2 bg-white w-full overflow-auto h-[70vh] p-2 rounded-xl mt-3 flex-col">
            @foreach ($superAdmins as $admin)
                <div onclick="window.location.href='{{ route('customer.chat.show', $admin->id) }}'" class="customer-container flex gap-2 p-2 hover:bg-gray-200 rounded-lg cursor-pointer">
                    <i class="fa-solid fa-user text-white text-2xl bg-[#005382] p-5 rounded-full"></i>
                    <div class="flex-1">
                        <p class="text-xl font-bold">{{ $admin->s_admin_username }}</p>
                        @if($admin->last_message || $admin->last_file)
                            @php
                                $timeDifference = \Carbon\Carbon::parse($admin->last_message_time)->diffInSeconds(now());
                                if ($timeDifference < 60) {
                                    $timeText = "Now";
                                } elseif ($timeDifference < 3600) {
                                    $timeText = floor($timeDifference / 60) . " min" . (floor($timeDifference / 60) > 1 ? "s" : "") . " ago";
                                } else {
                                    $timeText = \Carbon\Carbon::parse($admin->last_message_time)->format('h:i A');
                                }
                            @endphp
        
                            @if($admin->last_sender_id == $authUserId)
                                <strong>You:</strong> 
                                {{ $admin->last_file ? 'File' : $admin->last_message }}
                            @else
                                <strong>{{ $admin->s_admin_username }}:</strong> 
                                {{ $admin->last_file ? 'File' : $admin->last_message }}
                            @endif
                            <span class="text-xs text-gray-400"> • {{ $timeText }}</span>
                        @else
                            <p class="text-gray-500">No messages yet.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Terms and Conditions Agreement --}}
        <div class="fixed bg-black/40 w-full h-full top-0 left-0 p-10" id="terms&conditions">
            <div class="modal w-full md:w-[40%] bg-white rounded-lg p-5 flex flex-col gap-2 m-auto">
                <h1 class="text-[#005382] text-2xl font-bold">Terms and Conditions</h1>
                <hr class="border border-gray-200">
                <p class="text-justify mt-5">Before you proceed, please take a moment to review our <span class="text-[#005382] underline cursor-pointer" onclick="opentermsandconditions()">Terms and Conditions.</span>
                    By clicking "Accept", you acknowledge that you have read and agree to the terms and conditions.</p>
                
                <div class="flex items-center mt-5">
                    <input type="checkbox" name="agree" id="agree" class="mr-2 w-5 h-5" required>
                    <label for="agree">I have read and agree to the</label>
                    <span class="text-[#005382] underline cursor-pointer ml-2" onclick="opentermsandconditions()">Terms and Conditions</span>
                </div>

                <button id="proceed" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer w-fit" disabled>Proceed</button>
            </div>
        </div>

        {{-- Terms and Conditions Modal --}}
        <div class="fixed bg-black/40 w-full h-full top-0 left-0 p-10 hidden" id="terms&conditionsletter">
            <div class="modal w-full md:w-[60%] bg-white rounded-lg p-5 flex flex-col gap-2 m-auto relative">
                <x-modalclose click="closetermsandconditions"/>
                <h1 class="text-[#005382] text-2xl font-bold">Terms and Conditions</h1>
                <hr class="border border-gray-200">
                <div class="flex flex-col gap-2">
                    <p>1. The chat feature is for customer inquiries, order-related concerns, and support only.</p>
                    <p>2. Customers must use respectful and appropriate language at all times. </p>
                    <p>3. Spamming, offensive messages, or any form of harassment is strictly prohibited.</p>
                    <p>4. Messages may be monitored by admins for security and support purposes.</p>
                    <p>5. Do not share personal, sensitive, or confidential information in the chat.</p>
                    <p>6. Chat history may be stored for reference but will not be shared with third parties.</p>
                    <p>7. RMPOIMS is not responsible for miscommunication or errors made in chat conversations.</p>
                    <p>8. By using the chat, you agree to these terms.</p>
                </div>
            </div>
        </div>
    </main>
</body>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkbox = document.getElementById("agree");
        const proceedButton = document.getElementById("proceed");
        const termsAndConditions = document.getElementById("terms&conditions");

        proceedButton.disabled = false;

        checkbox.addEventListener("change", function () {
            if (checkbox.checked) {
                proceedButton.disabled = false;
            }
            else {
                proceedButton.disabled = true;
            }
                     
        });

        proceedButton.addEventListener("click", function () {
            if (!checkbox.checked) {
                Swal.fire({
                    icon: 'error',
                    title: 'Sorry',
                    text: 'Please agree to the terms and conditions to proceed!',
                    confirmButtonColor: "#005382"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('customer.order') }}";
                    }
                });
            } else {
                termsAndConditions.style.display = "none";
            }
        });
    });

    function opentermsandconditions() {
        const termsAndConditions = document.getElementById("terms&conditionsletter");
        termsAndConditions.style.display = "block";
    }

    function closetermsandconditions() {
        const termsAndConditions = document.getElementById("terms&conditionsletter");
        termsAndConditions.style.display = "none";
    }
</script>
</html>

