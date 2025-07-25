<meta name="csrf-token" content="{{ csrf_token() }}">


<!-- Sidebar -->
<div class="flex flex-col gap-2 h-full w-0 fixed top-0 -left-32 bg-white z-50 p-5 list-none transition-all duration-500" id="sidebar">
    <div class="p-3 flex flex-col relative">
        <img src="{{ asset('image/Logowname.png') }}" alt="" class="w-[130px] self-center">
        <hr class="mt-2">
        <div onclick="closeSidebar()" class="w-10 h-10 bg-[#005382] shadow-md flex items-center justify-center absolute -top-5 -right-10 rounded-md hover:cursor-pointer">
            <span class="text-6xl text-white font-bold">&times;</span>
        </div>
    </div>
    <ul class="flex-1 flex flex-col gap-5 pt-5">
        <li><a href="{{ route('customer.dashboard') }}" class="text-md"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li><a href="{{ route('customer.order') }}" class="text-md"><i class="fa-solid fa-cart-shopping"></i> Make an Order</a></li>
        <li><a href="{{ route('customer.manageorder') }}" class="text-md"><i class="fa-solid fa-list-check"></i> Manage Order</a></li>
        <li><a href="{{ route('customer.history') }}" class="text-md"><i class="fa-regular fa-clock"></i> Order History</a></li>
        <li>
            <a href="{{ route('customer.chat.index') }}" id="chatSidebar" class="text-md relative">
                <i class="fa-brands fa-rocketchat"></i> Chat
                @if ($totalUnreadMessages > 0)
                    <span class="absolute top-2.5 right-2 bg-red-500 text-white p-1 px-2 rounded-full text-xs">
                        {{ $totalUnreadMessages }}
                    </span>
                @endif
            </a>
        </li>
        <li><a href="{{ route('customer.manageaccount') }}" class="text-md"><i class="fa-solid fa-gear"></i> Account</a></li>
        <li><a href="#" id="openReviewModalsidebar" class="text-md"><i class="fa-solid fa-star"></i> Leave a Review</a></li>
        <li class="mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-md uppercase bg-[#005382] p-2 text-white rounded-lg w-full">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>
<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden" onclick="closeSidebar()"></div>

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
                <textarea name="comment" id="commentSidebar" rows="4" class="w-full p-2 border rounded-md" placeholder="Tell us what you liked or what could be improved..."></textarea>
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

 
    document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const currentLocation = window.location.href;
    document.querySelectorAll('#sidebar a').forEach(link => {
        if (link.href === currentLocation) {
            link.classList.add('active');
        }
    });

    // This function is now called by your header's burger menu
    window.openSidebar = function() {
        sidebar.classList.add('left-0', 'w-[300px]');
        overlay.classList.remove('hidden');
    }

    // This function is called by the close button and the overlay
    window.closeSidebar = function() {
        sidebar.classList.remove('left-0', 'w-[300px]');
        overlay.classList.add('hidden');
    }

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
                alert('Something went wrong. Please try again.');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('An error occurred. Please try again.');
        });
    });
});
</script>
