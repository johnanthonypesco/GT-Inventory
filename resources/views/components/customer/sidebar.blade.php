<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden"></div>
<!-- Sidebar -->
<div class="flex flex-col gap-2 h-full w-0 fixed top-0 -left-32 bg-white z-50 list-none transition-all duration-500" id="sidebar">
    <div class="p-3 flex flex-col relative">
        <img src="{{ asset('image/Logowname.png') }}" alt="" class="w-[130px] self-center">
        <div onclick="closeSidebar()" class="flex items-center justify-center absolute top-5 right-5 rounded-md hover:cursor-pointer">
            <i class="fa-solid fa-x text-2xl text-black/80"></i>
        </div>
    </div>
    <ul class="list-none flex flex-col py-2 gap-[1px] overflow-y-auto">

        {{-- HOME --}}
        <div class="text-[13px] capitalize p-1 w-full text-gray-500 font-semibold flex items-center justify-between gap-2">Home</div>
        <li>
            <a href="{{ route('customer.dashboard') }}" class="mt-1 flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 relative hover:bg-gray-100 hover:text-black {{ request()->is('customer/dashboard') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                <i class="fa-solid fa-gauge text-base"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('customer.order') }}" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 relative hover:bg-gray-100 hover:text-black {{ request()->is('customer/order') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                <i class="fa-solid fa-cart-shopping text-base"></i>
                <span>Make an Order</span>
            </a>
        </li>
        
        {{-- COMMUNICATION --}}
        <div class="text-[13px] capitalize p-1 w-full text-gray-500 font-semibold mt-2 flex items-center justify-between">Communication</div>
        <li>
            <a href="{{ route('customer.chat.index') }}" id="chatNav" class="mt-1 relative flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('customer/chat*') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                <i class="fa-brands fa-rocketchat text-base"></i>
                <span>Chat</span>
                @if ($totalUnreadMessages > 0)
                    <div class="absolute top-3 right-1 bg-red-500 px-1 rounded-full text-sm text-white">
                        <span>{{ $totalUnreadMessages }}</span>
                    </div>
                @endif
            </a>
        </li>

        {{-- MANAGEMENT --}}
        <div class="text-[13px] capitalize p-1 w-full text-gray-500 font-semibold mt-2 flex items-center justify-between">Management</div>
        <li>
            <a href="{{ route('customer.manageorder') }}" class="mt-1 flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('customer/manageorder') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                <i class="fa-solid fa-list-check text-base"></i>
                <span>Manage Order</span>
            </a>
        </li>
        <li>
            <a href="{{ route('customer.manageaccount') }}" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('customer/manageaccount') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                <i class="fa-solid fa-gear text-base"></i>
                <span>Manage Account</span>
            </a>
        </li>

        {{-- HISTORY & REVIEW --}}
        <div class="text-[13px] capitalize p-1 w-full text-gray-500 font-semibold mt-2 flex items-center justify-between">History & Review</div>
        <li>
            <a href="{{ route('customer.history') }}" class="mt-1 flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black {{ request()->is('customer/history') ? 'bg-[#0052821b] text-black border-l-4 border-blue-600' : '' }}">
                <i class="fa-regular fa-clock text-base"></i>
                <span>Order History</span>
            </a>
        </li>
        <li>
            <a href="#" id="openReviewModal" class="flex items-center gap-4 p-3 text-sm text-gray-600 font-regular transition-all duration-300 hover:bg-gray-100 hover:text-black">
                <i class="fa-solid fa-star text-base"></i>
                <span>Leave a Review</span>
            </a>
        </li>
    </ul>

    {{-- LOGOUT --}}
    <form action="{{ route('logout') }}" method="POST" class="mt-auto">
        @csrf
        <button type="submit" class="logout w-full text-sm text-left flex items-center gap-4 p-4 font-medium transition-all duration-300 relative bg-gray-100 text-black border-l-4 border-blue-600 hover:bg-gray-200">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout ...</span>
        </button>
    </form>
</div>
<!-- Review Modal -->
<div id="reviewModalsidebar" class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Leave a Review</h2>
        <form id="reviewFormsidebar">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Overall Rating</label>
                <div class="flex items-center" id="star-rating-sidebar">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fa-regular fa-star text-2xl text-gray-400 cursor-pointer" data-value="{{ $i }}"></i>
                    @endfor
                    <input type="hidden" name="rating" id="ratingValueSidebar" value="0">
                </div>
            </div>

            <div class="mb-4">
                <label for="commentSidebar" class="block text-gray-700 mb-2">Your Feedback</label>
                <textarea name="comment" id="commentSidebar" required rows="4" class="w-full p-2 border rounded-md" placeholder="Tell us what you liked or what could be improved..."></textarea>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="allow_public_display" class="mr-2">
                    <span class="text-sm text-gray-600">I agree to let my organization's name be shown with this review on the promotional page.</span>
                </label>
            </div>

            <div class="flex justify-end gap-4">
                <button type="button" id="closeReviewModalsidebar" class="py-2 px-4 bg-gray-200 rounded-lg">Cancel</button>
                <button type="submit" class="py-2 px-4 bg-[#005382] text-white rounded-lg">Submit Review</button>
            </div>
        </form>
        <div id="form-success-sidebar" class="hidden text-center p-4 text-green-700">
            <p>Thank you for your feedback!</p>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.remove('left-[0.1px]', 'w-[300px]');
        const overlay = document.getElementById('sidebar-overlay');
        overlay.classList.add('hidden');
    }
 
    document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const currentLocation = window.location.href;
    document.querySelectorAll('#sidebar a').forEach(link => {
        if (link.href === currentLocation) {
            link.classList.add('active');
        }
    });

    window.openSidebar = function() {
        sidebar.classList.add('left-[0.1px]', 'w-[300px]');
        overlay.classList.remove('hidden');
    }

    // This function is called by the close button and the overlay
    // window.closeSidebar = function() {
    //     sidebar.classList.remove('left-[0.1px]', 'w-[300px]');
    //     overlay.classList.add('hidden');
    // }

    // Handles closing when the window is resized
    window.addEventListener('resize', () => {
        window.closeSidebar();
    });


    // Chat refresh
    setInterval(() => {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newChat = doc.getElementById('chatSidebar');
                if (newChat) {
                    document.getElementById('chatSidebar').innerHTML = newChat.innerHTML;
                }
            });
    }, 5000);

    // Review Modal Logic
    const modal = document.getElementById('reviewModalsidebar');
    const openBtn = document.getElementById('openReviewModalsidebar');
    const closeBtn = document.getElementById('closeReviewModalsidebar');
    const reviewForm = document.getElementById('reviewFormsidebar');
    const stars = document.querySelectorAll('#star-rating-sidebar .fa-star');
    const ratingValue = document.getElementById('ratingValueSidebar');
    const successMessage = document.getElementById('form-success-sidebar');

    openBtn.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.remove('hidden');
        reviewForm.reset();
        successMessage.classList.add('hidden');
        reviewForm.style.display = 'block';
        resetStars();
    });

    closeBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    function resetStars() {
        stars.forEach(star => {
            star.classList.replace('fa-solid', 'fa-regular');
            star.classList.remove('text-yellow-400');
        });
    }

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const value = parseInt(star.getAttribute('data-value'), 10);
            ratingValue.value = value;
            resetStars();
            for (let i = 0; i < value; i++) {
                stars[i].classList.replace('fa-regular', 'fa-solid');
                stars[i].classList.add('text-yellow-400');
            }
        });
    });

    reviewForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(reviewForm);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch("{{ route('customer.review.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                reviewForm.style.display = 'none';
                successMessage.classList.remove('hidden');
                setTimeout(() => modal.classList.add('hidden'), 2000);
            } else {
                alert('Dont forget the stars!');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('An error occurred. Please try again.');
        });
    });
});
</script>
