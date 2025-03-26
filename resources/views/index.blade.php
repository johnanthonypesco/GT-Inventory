<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Promotional Page of RCT Med Pharma</title>
</head>
<body>
    <header class="flex justify-between items-center px-10 lg:px-20 h-24">
        <img src="{{ asset('image/Logowname.png') }}" alt="rctmedpharma" class="hidden lg:block w-[120px]">
        
        <nav class="bg-gray-200 flex items-center gap-10 px-5 py-3 rounded-full font-semibold text-gray-700"> 
            <a href="#home">Home</a>
            <a href="#">Products</a>
            <a href="#">About</a>
            <a href="#">Contact Us</a>
        </nav>

        <div class="hidden lg:flex gap-4 items-center text-xl justify-center">
            <i class="fa-solid fa-search p-2 bg-green-500/20 rounded-full text-gray-700"></i>
            <a href="{{ route('login') }}" class="bg-[#005382] py-1 px-4 rounded-full text-white">Login</a>
        </div>
    </header>

    <main class="px-10 lg:px-24">
        <section id="home" class="relative">
            <div class="flex justify-center items-center flex-col gap-3 z-10 relative lg:mt-24">
                <h1 class="text-6xl font-bold lg:w-[80%] text-center">Order Your Medication Effortlessly Anytime & Anywhere</h1>
                <p class="text-2xl text-gray-700">Get your medication delivered to your doorstep with ease and convenience</p>
    
                <a href="" class="bg-[#005382] py-2 px-4 rounded-full text-white w-fit flex items-center gap-2 mt-5"><i class="fa-solid fa-cart-shopping"></i>Order Now</a>
            </div>

            <div class="bg-gray-200 w-[700px] h-[700px] absolute top-10 right-0 left-0 mx-auto rounded-[100%] z-1"></div>
        </section>
        
    </main>
</body>
</html>