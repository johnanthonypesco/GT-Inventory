{{-- view/index.blade.php --}}
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
            {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

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
            <button id="inquire" onclick="window.location.href = '#inquire'" class="bg-[#0097D3] w-fit px-5 py-2 rounded-lg font-semibold text-white hover:bg-[#005382] hover:cursor-pointer hover:-translate-y-1 transition-all duration-200">Reach Us</button>
            
            {{-- <a href="{{ route('beta.register') }}" class="bg-red-600 animate-pulse w-fit px-5 py-2 rounded-lg font-semibold text-white">Join The Beta Test Program</a> --}}
        </nav>

        <i class="fa-solid fa-bars text-xl lg:hidden cursor-pointer" id="hamburger"></i>
    </header>    

    <main class="mt-20 mb-20 main">
        <section id="home" class="flex flex-col-reverse px-5 lg:flex-row justify-center lg:px-24">
            <div class="flex flex-col gap-5 lg:w-1/2 lg:gap-10 lg:mt-20" id="content-left">
                <h1 id="h1" class="text-5xl font-bold">Connect with <span class="text-[#0097D3]">RCT Med Pharma</span> Anytime, Anywhere!</h1>
                <p id="p" class="text-lg">
                </p>
                <div class="flex gap-5">
                    <a id="button1" href="#inquire" class="bg-[#0097D3] w-fit px-5 py-2 rounded-lg font-semibold text-white hover:-translate-y-1 transition-transform duration-300">Reach Us</a>
                    <a id="button2" href="#about" class="border border-[#0097D3] w-fit px-5 py-2 rounded-lg font-semibold hover:-translate-y-1 transition-transform duration-300">About Us</a>
                </div>
            </div>
            <div id="bouncy" class="flex justify-center items-center lg:w-1/2">
                <img src="{{ asset('image/Medecine Bg.png') }}" alt="Landing Page" class="w-[450px] h-[400px] lg:w-[600px] lg:h-[550px]">
            </div>
        </section>
    
        <div class="flex mt-20 lg:mt-10 justify-center gap-2" id="content-left">
            <hr class="bg-[#0097D3] rounded-lg w-[50px] h-1">
            <hr class="bg-[#0097D3] rounded-lg w-[20px] h-1">
        </div>
    
        <section id="about" class="scroll-mt-20 lg:scroll-mt-15">
            <h1 class="text-xl text-[#084876] text-center font-bold mt-10" id="content-right">About Us</h1>

            <div class="flex flex-col justify-center items-center mt-10 px-5 gap-5 lg:flex-row lg:px-24 lg:gap-10">
                <div id="content-left">
                    <img src="{{ asset('image/About Us.png') }}" alt="About Us" class="lg:w-[500px] lg:h-[450px] w-[450px] h-[450px] ">
                </div>

                <div class="flex flex-col gap-5 lg:w-1/2" id="content-right">
                    <p class="text-lg">At <span class="text-[#084876] font-semibold">RCT Med Pharma</span>, we are dedicated to delivering precision, reliability, and excellence in pharmaceutical distribution, treating every client partnership with the utmost care and responsibility.</p>
                   @forelse ($content as $content)
                        <x-promotionalpage.aboutuscontent :description="$content->aboutus1" />
                        <x-promotionalpage.aboutuscontent :description="$content->aboutus2" />
                        <x-promotionalpage.aboutuscontent :description="$content->aboutus3" />
                    @empty
                        <p class="text-center text-gray-500">No about us content available.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <div class="flex mt-20 lg:mt-15 justify-center gap-2" id="content-left">
            <hr class="bg-[#0097D3] rounded-lg w-[50px] h-1">
            <hr class="bg-[#0097D3] rounded-lg w-[20px] h-1">
        </div>

        <section class="mt-10 relative flex flex-col items-center scroll-mt-24" id="products">
            <div class="bg-[#084876]/20 rounded-full w-[70%] h-[100%] blur-3xl absolute z-0"></div>
            <h1 class="text-xl text-[#084876] text-center font-bold" id="content-right">Our Products</h1>

            <div class="flex gap-5 mt-10 z-1 relative">
                <button onclick="setFilter('all')" class="text-[#084876] font-bold border-b border-[#084876] filter-btn" data-filter="all" id="content-right">All Products</button>
                <button onclick="setFilter('injectables')" class="text-gray-600 font-bold filter-btn" data-filter="injectables" id="content-left">Injectables</button>
                <button onclick="setFilter('oral')" class="text-gray-600 font-bold filter-btn" data-filter="oral" id="content-left">Oral</button>
            </div>

            <div class="flex w-full lg:w-[70%] z-1 relative gap-5 overflow-hidden p-5 justify-center" id="content-right">
                @forelse($enabledProducts as $product)
                    <div id="product-scroll" class="product-card" data-filter="{{ strtolower($product->form) }}">
                        <x-promotionalpage.product 
                            :image="$product->img_file_path"
                            genericname="{{ $product->generic_name }}"
                            brandname="{{ $product->brand_name }}"
                            form="{{ $product->form }}"
                        />  
                    </div>
                @empty
                    <p class="text-center text-gray-500">No products available.</p>
                @endforelse
            </div>

            @if($enabledProducts->isNotEmpty())
                <button 
                    class="bg-[#0097D3] text-white px-5 py-2 rounded mt-10 font-semibold cursor-pointer z-5 relative" 
                    id="viewallproducts"
                    onclick="openProductsModal()">
                    View All Products
                </button>
            @endif
        </section>
        
        

        <div class="flex mt-24 justify-center gap-2" id="content-left">
            <hr class="bg-[#0097D3] rounded-lg w-[50px] h-1">
            <hr class="bg-[#0097D3] rounded-lg w-[20px] h-1">
        </div>

        <section id="inquire" class="mt-10 flex flex-col items-center px-5 scroll-mt-12">
            <h1 class="text-xl text-[#084876] text-center font-bold mt-5" id="content-right">Get in Touch</h1>
            <h1 class="text-4xl text-[#084876] text-center font-bold mt-5" id="content-left">Reach Us</h1>

            <div class="flex flex-col lg:flex-row gap-10 mt-10 border rounded-lg p-5 shadow-md w-full lg:w-[70%]" id="content-right">
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
                    @if ($content)
                        <p class="text-lg flex justify-center lg:justify-start gap-2">
                            <i class="fa-solid fa-phone"></i> Phone: {{ $content->contact_number }}
                        </p>
                        <p class="text-lg flex justify-center lg:justify-start gap-2">
                            <i class="fa-solid fa-message"></i> Email: {{ $content->email }}
                        </p>
                        <p class="text-lg flex justify-center lg:justify-start gap-2">
                            <i class="fa-solid fa-location-dot"></i> Address: {{ $content->address }}
                        </p>
                    @else
                        <p class="text-center text-gray-500">No contact details available.</p>
                    @endif
                </div>
            </div>
        </section>

        <section id="reviews" class="mt-20 px-5 lg:px-24">
            <h2 class="text-xl font-bold text-center text-[#084876]" id="content-right">What Our Customers Say</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-10" id="content-left">
                @forelse($reviews as $review)
                    <div class="bg-white p-4 border rounded shadow">
                        <div class="flex gap-1 mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $review->rating)
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                @else
                                    <i class="fa-regular fa-star text-gray-300"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="italic text-gray-700">"{{ $review->comment }}"</p>
                        @if ($review->allow_public_display && $review->user)
                            <p class="text-sm text-gray-600 mt-2">– {{ $review->user->name }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-gray-500">No public reviews yet.</p>
                @endforelse
            </div>
        </section>
    </main>

    <div class="fixed hidden bg-black/40 w-full h-full top-0 left-0 lg:p-10 p-5 z-50" id="productsmodal">
            <div class="modal bg-white rounded-md w-full h-full relative lg:p-10 p-5 ">
                <x-modalclose id="closeproductsmodal"/>
                <h1 class="text-xl text-[#084876] font-bold text-center lg:text-left">All Products</h1>

                <div class="mt-5 flex gap-4 justify-center lg:justify-start">
                    <button class="text-[#084876] font-md text-xl border-b border-[#084876] filter-btn" data-filter="all" onclick="applyModalFilter('all')">All Products</button>
                    <button class="text-gray-600 font-md text-xl filter-btn" data-filter="injectables" onclick="applyModalFilter('injectables')">Injectables</button>
                    <button class="text-gray-600 font-md text-xl filter-btn" data-filter="oral" onclick="applyModalFilter('oral')">Oral</button>
                </div>

                <div class="flex flex-wrap gap-5 mt-5 overflow-y-auto h-[600px] lg:h-[400px] justify-center">
                    @forelse($enabledProducts as $product)
                        <div class="product-card" data-filter="{{ strtolower($product->for) }}">
                            <x-promotionalpage.product 
                                :image="$product->img_file_path"
                                genericname="{{ $product->generic_name }}"
                                brandname="{{ $product->brand_name }}"
                                form="{{ $product->form }}"
                            />
                        </div>
                    @empty
                        <p class="text-center text-gray-500">No products available.</p>
                    @endforelse
                </div>
            </div>
        </div>

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

    {{-- loader --}}
    {{-- <x-loader/> --}}
    {{-- loader --}}
</body>
<script src="https://cdn.botpress.cloud/webchat/v3.2/inject.js" defer></script>
<script src="https://files.bpcontent.cloud/2025/08/11/15/20250811151855-NQYCVRWL.js" defer></script>
    
    <!--<script chatbot_id="6898c15e3362e02152ae1688" data-type="default" src="https://app.thinkstack.ai/bot/thinkstackai-loader.min.js"></script>-->
<!--<script src="//code.tidio.co/smjz9pf1qeaxphqxmrdbw46qxzlpfv6s.js" async></script>-->
<script src="{{ asset('js/landingpage/index.js') }}"></script>
</html>