@if (session ('success'))
    <div id="successAlert" class="w3 fixed top-5 right-5 bg-green-500 text-white py-3 px-6 rounded-lg shadow-lg z-101 flex items-center gap-3 z-[101]">
        <i class="fa-solid fa-circle-check text-2xl"></i>
        <div>
            <p class="font-bold">Success!</p>
            <p id="successMessage"></p>
        </div>
    </div>
    <script>
        const audio = new Audio('{{ asset('sounds/Fart sound effect 4.mp3') }}');
        audio.play();
    </script>
@elseif (session ('error'))
    <div id="errorAlert" class="w3 fixed top-5 right-5 bg-red-500 text-white py-3 px-6 rounded-lg shadow-lg z-101 flex items-center gap-3">
        <i class="fa-solid fa-circle-xmark text-2xl"></i>
        <div>
            <p class="font-bold">Error!</p>
            <p>{{ session('error') }}</p>
        </div>
    </div>
@endif