<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">
    <title>RCT Med Pharma</title>
</head>
<body>
    <header class="flex justify-between items-center bg-white h-14 px-5 shadow-md lg:h-16 lg:px-24 fixed w-full top-0 z-10">
        <div class="flex items-center gap-2">
            <img src="{{ asset('image/Logolandingpage.png') }}" alt="Logo" class="w-12 h-12">
            <h1 class="font-semibold text-lg uppercase">RCT Med Pharma</h1>
        </div>

        <nav class="absolute flex flex-col gap-5 top-14 left-0 w-full h-fit px-5 py-5 bg-white shadow-md hidden lg:block lg:static lg:shadow-none lg:py-0 lg:w-fit" id="nav">
            <a href="#home" class="lg:mr-12 font-semibold hover:text-[#0097D3]">Home</a>
            <a href="#about" class="lg:mr-12 font-semibold hover:text-[#0097D3]">About Us</a>
            <a href="#products" class="lg:mr-12 font-semibold hover:text-[#0097D3]">Products</a>
            <a href="#inquire" class="bg-[#0097D3] w-fit px-5 py-2 rounded-lg font-semibold text-white">Reach Us</a>
        </nav>

        <i class="fa-solid fa-bars text-xl lg:hidden cursor-pointer" id="hamburger"></i>
    </header>

    <main class="mt-20 mb-20">
        <section id="home" class="flex flex-col-reverse px-5 lg:flex-row justify-center lg:px-24">
            <div class="flex flex-col gap-5 lg:w-1/2 lg:gap-10 lg:mt-20" id="content-left">
                <h1 class="text-5xl font-bold">Connect with <span class="text-[#0097D3]">RCT Med Pharma</span> Anytime, Anywhere!</h1>
                <p class="text-lg">Our secure and efficient system allows you to place orders, track inventory, 
                    and manage transactions with ease. Experience seamless healthcare solutions 
                    at your fingertips, no matter where you are.
                </p>
                <div class="flex gap-5">
                    <a href="#inquire" class="bg-[#0097D3] w-fit px-5 py-2 rounded-lg font-semibold text-white">Reach Us</a>
                    <a href="#about" class="border border-[#0097D3] w-fit px-5 py-2 rounded-lg font-semibold">About Us</a>
                </div>
            </div>
            <div id="content-right" class="flex justify-center items-center lg:w-1/2">
                <img src="{{ asset('image/Medecine Bg.png') }}" alt="Landing Page" class="w-[450px] h-[450px] lg:w-[600px] lg:h-[600px]">
            </div>
        </section>
    
        <div class="flex mt-20 lg:mt-10 justify-center gap-2">
            <hr class="bg-[#0097D3] rounded-lg w-[50px] h-1">
            <hr class="bg-[#0097D3] rounded-lg w-[20px] h-1">
        </div>
    
        <section id="about" class="scroll-mt-20 lg:scroll-mt-15">
            <h1 class="text-xl text-[#084876] text-center font-bold mt-10">About Us</h1>

            <div class="flex flex-col justify-center items-center mt-10 px-5 gap-5 lg:flex-row lg:px-24 lg:gap-10">
                <div id="content-left">
                    <img src="{{ asset('image/About Us.png') }}" alt="About Us" class="lg:w-[500px] lg:h-[450px] w-[450px] h-[450px] ">
                </div>

                <div class="flex flex-col gap-5 lg:w-1/2" id="content-right">
                    <p class="text-lg">At <span class="text-[#084876] font-semibold">RCT Med Pharma</span>, we are dedicated to delivering precision, reliability, and excellence in pharmaceutical distribution, treating every client partnership with the utmost care and responsibility.</p>
                    <x-promotionalpage.aboutuscontent title="Reliable Supply Chain" description="We ensure a consistent and timely delivery of high-quality pharmaceutical products, helping healthcare providers meet their patients' needs without disruption." />
                    <x-promotionalpage.aboutuscontent title="Access to Industry Experts" description="Our team includes licensed pharmacists, logistics specialists, and compliance officers, ensuring adherence to industry regulations and best practices." />
                    <x-promotionalpage.aboutuscontent title="Continuous Innovation" description="We embrace cutting-edge technology and automation in our inventory and ordering system (RMPOIMS) to enhance efficiency, accuracy, and customer satisfaction." />
                </div>
            </div>
        </section>

        <div class="flex mt-20 lg:mt-15 justify-center gap-2">
            <hr class="bg-[#0097D3] rounded-lg w-[50px] h-1">
            <hr class="bg-[#0097D3] rounded-lg w-[20px] h-1">
        </div>

        <section class="mt-10 relative flex flex-col items-center scroll-mt-24" id="products">
            <div class="bg-[#084876]/20 rounded-full w-[70%] h-[100%] blur-3xl absolute z-0"></div>
            <h1 class="text-xl text-[#084876] text-center font-bold">Our Products</h1>
            
            <div class="flex gap-5 mt-10 z-1 relative">
                <button class="text-[#084876] font-bold border-b border-[#084876] filter-btn" data-filter="all">All Products</button>
                <button class="text-gray-600 font-bold filter-btn" data-filter="injectables">Injectables</button>
                <button class="text-gray-600 font-bold filter-btn" data-filter="oral">Oral</button>
            </div>
            <div class="flex w-[90%] z-1 relative gap-5 overflow-x-auto p-5">
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Ceftriaxone" brandname="Ceftriaxone" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Cefazoline" brandname="Cefazovit" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Ceftazidime" brandname="Ceftazivit" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Ceftrialisis" brandname="NA" form="Oral" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Dexamethasone" brandname="Dexavit" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Furosemide" brandname="Furotalis" form="Oral" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Ketoprofen" brandname="Ketonix" form="Oral" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Losartan" brandname="Losil" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Neotalis" brandname="NA" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Ranitidine" brandname="Ranicid`" form="Oral" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Pantoprazole" brandname="Pantrex" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Sodium Bicarbonate" brandname="Sodicarb" form="Oral" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Vitamin A" brandname="Vitaroxima" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Betamethasone" brandname="Vitasone" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Metronidazole" brandname="Vitazol" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Multivitamins" brandname="Vitral" form="Oral" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Vitamin K" brandname="Ambivit K" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Ampicillin" brandname="Amiphil" form="Oral" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Diphenhydramine" brandname="Diphenpors" form="Oral" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Gentamicin" brandname="Gentacare" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Vitamin B Complex" brandname="Neurobe" form="Injectables" />
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Omeprazole" brandname="Oprex" form="Oral" /> 
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Cephalexin" brandname="Sapharin" form="Oral" />   
                <x-promotionalpage.product image="ceftriaxone.png" genericname="Clindamycin" brandname="Tidact" form="Oral" />   
            </div>
            <button class="bg-[#0097D3] text-white px-5 py-2 rounded mt-10 font-semibold cursor-pointer z-5 relative" id="viewallproducts">View All Products</button>
        </section>

        <div class="hidden fixed bg-black/40 w-full h-full top-0 left-0 lg:p-20 p-8 z-10" id="productsmodal">
            <div class="modal bg-white rounded-md w-full h-full relative lg:p-10 p-5">
                <x-modalclose id="closeproductsmodal"/>
                <h1 class="text-xl text-[#084876] font-bold text-center lg:text-left">All Products</h1>
                <div class="mt-5 flex gap-2 justify-center lg:justify-start">
                    <button class="text-[#084876] font-md border-b border-[#084876] filter-btn" data-filter="all">All Products</button>
                    <button class="text-gray-600 font-md filter-btn" data-filter="injectables">Injectables</button>
                    <button class="text-gray-600 font-md filter-btn" data-filter="oral">Oral</button>
                </div>
                <div class="flex flex-wrap gap-5 mt-5 overflow-y-auto h-[500px] lg:h-[370px] justify-center">
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Ceftriaxone" brandname="Ceftriaxone" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Cefazoline" brandname="Cefazovit" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Ceftazidime" brandname="Ceftazivit" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Ceftrialisis" brandname="NA" form="Oral" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Dexamethasone" brandname="Dexavit" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Furosemide" brandname="Furotalis" form="Oral" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Ketoprofen" brandname="Ketonix" form="Oral" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Losartan" brandname="Losil" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Neotalis" brandname="NA" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Ranitidine" brandname="Ranicid`" form="Oral" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Pantoprazole" brandname="Pantrex" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Sodium Bicarbonate" brandname="Sodicarb" form="Oral" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Vitamin A" brandname="Vitaroxima" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Betamethasone" brandname="Vitasone" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Metronidazole" brandname="Vitazol" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Multivitamins" brandname="Vitral" form="Oral" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Vitamin K" brandname="Ambivit K" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Ampicillin" brandname="Amiphil" form="Oral" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Diphenhydramine" brandname="Diphenpors" form="Oral" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Gentamicin" brandname="Gentacare" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Vitamin B Complex" brandname="Neurobe" form="Injectables" />
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Omeprazole" brandname="Oprex" form="Oral" /> 
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Cephalexin" brandname="Sapharin" form="Oral" />   
                    <x-promotionalpage.product image="ceftriaxone.png" genericname="Clindamycin" brandname="Tidact" form="Oral" /> 
                </div>
            </div>
        </div>

        <div class="flex mt-24 justify-center gap-2">
            <hr class="bg-[#0097D3] rounded-lg w-[50px] h-1">
            <hr class="bg-[#0097D3] rounded-lg w-[20px] h-1">
        </div>

        <section id="inquire" class="mt-10 flex flex-col items-center px-5 scroll-mt-12">
            <h1 class="text-xl text-[#084876] text-center font-bold mt-5">Get in Touch</h1>
            <h1 class="text-4xl text-[#084876] text-center font-bold mt-5">Reach Us</h1>

            <div class="flex flex-col lg:flex-row gap-10 mt-10 border rounded-lg p-5 shadow-md w-full lg:w-[70%]">
                <div class="w-full max-w-[450px] aspect-square sm:aspect-video">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6329.863448083731!2d120.5903826362913!3d15.430930184127268!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3396c7a8904c55af%3A0xcc636500bd6a58c4!2sFARMACIA%20SAN%20MIGUEL!5e1!3m2!1sen!2sph!4v1752755129585!5m2!1sen!2sph" 
                        class="w-full h-full border-0 rounded-md"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                <div class="w-full lg:w-1/2 flex flex-col gap-4">
                    <p class="text-lg flex justify-center lg:justify-start gap-2">
                        <i class="fa-solid fa-phone"></i>Phone: 123-456-789
                    </p>
                    <p class="text-lg flex justify-center lg:justify-start gap-2">
                        <i class="fa-solid fa-message"></i>Email: rctmedpharma@gmail.com
                    </p>
                    <p class="text-lg flex justify-center lg:justify-start gap-2">
                        <i class="fa-solid fa-location-dot"></i>Address: Riverside Street, Barangay San Miguel, Tarlac City, Tarlac, Philippines
                    </p>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-[#084876] text-white p-5 w-full">
        <div class="flex gap-1 items-center justify-center">
            <img src="{{ asset('image/Logolandingpage.png') }}" alt="Logo" class="w-12 h-12">
            <h1 class="font-semibold text-md uppercase">RCT Med Pharma</h1>
        </div>

        <nav class="flex items-center justify-center gap-10 mt-5">
            <a href="#home" class="text-md font-regular hover:text-[#0097D3]">Home</a>
            <a href="#about" class="text-md font-regular hover:text-[#0097D3]">About</a>
            <a href="#products" class="text-md font-regular hover:text-[#0097D3]">Products</a>
        </nav>

        <p class="text-md text-center mt-5">Copyright © RCT MED PHARMA. All Rights Reserve</p>
    </footer>
</body>
<script src="{{ asset('js/landingpage/index.js') }}"></script>
</html>