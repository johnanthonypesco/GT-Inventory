<meta name="csrf-token" content="{{ csrf_token() }}">


<nav id="customernavbar" class="h-[100vh] hidden lg:flex flex-col p-5 w-0 lg:w-[16%] bg-white fixed top-0 left-0 opacity-0 z-[50] shadow-none">
    <div class="flex flex-col">
        <img src="{{ asset('image/Logowname.png') }}" alt="" class="w-[130px] self-center">
    </div>

    <ul class="flex flex-col pt-5">
        <div class="flex flex-col gap-2">
            <span class="w-full border-b-2 text-[#005382]/70 font-semibold mb-1 text-sm flex justify-between items-center">Home</span>
            <a href="{{ route('customer.dashboard') }}" class="text-sm"><i class="fa-solid fa-gauge"></i>Dashboard</a>
            <a href="{{ route('customer.order') }}" class="text-sm {{ request()->is('customer/order') ? 'active' : '' }}"><i class="fa-solid fa-cart-shopping {{ request()->is('customer/order') ? 'text-white' : '' }}"></i>Make an Order</a>
        </div>
        
        <div class="flex flex-col gap-2 mt-4">
            <span class="w-full border-b-2 text-[#005382]/70 font-semibold mb-1 text-sm flex justify-between items-center">Communication</span>
            <a href="{{ route('customer.chat.index') }}" id="chatNav" class="text-sm relative">
                <i class="fa-brands fa-rocketchat"></i>Chat
    
                @if ($totalUnreadMessages > 0)
                    <span class="absolute top-2.5 right-2 bg-red-500 text-white p-1 px-2 rounded-full text-xs">
                        {{ $totalUnreadMessages }}
                    </span>
                @endif
            </a>
        </div>

        <div class="flex flex-col gap-2 mt-4">
            <span class="w-full border-b-2 text-[#005382]/70 font-semibold mb-1 text-sm flex justify-between items-center">Management</span>            
            <a href="{{ route('customer.manageorder') }}" class="text-sm {{ request()->is('customer/manageorder') ? 'active' : '' }}"><i class="fa-solid fa-list-check {{ request()->is('customer/manageorder') ? 'text-white' : '' }}"></i>Manage Order</a>
            <a href="{{ route('customer.manageaccount') }}" class="text-sm"><i class="fa-solid fa-gear"></i>Manage Account</a>
        </div>


        <div class="flex flex-col gap-2 mt-4">
            <span class="w-full border-b-2 text-[#005382]/70 font-semibold mb-1 text-sm flex justify-between items-center">History & Review</span>
            <a href="{{ route('customer.history') }}" class="text-sm {{ request()->is('customer/history') ? 'active' : '' }}"><i class="fa-regular fa-clock {{ request()->is('customer/history') ? 'text-white' : '' }}"></i>Order History</a>
            <a href="#" id="openReviewModal" class="text-sm"><i class="fa-solid fa-star"></i>Leave a Review</a>
        </div>

    </ul>

    <form action="{{ route('logout') }}" method="POST" class="mt-auto">
        @csrf
        <button type="submit" class="flex items-center gap-2 text-sm uppercase bg-[#005382] p-2 text-white rounded-lg w-full">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
    </form>
</nav>

 <div id="reviewModal" class="modal fixed inset-0 bg-black bg-opacity-50 z-[51] flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Leave a Review</h2>
        <form id="reviewForm">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Overall Rating</label>
                <div class="flex items-center" id="star-rating">
                    <i class="fa-regular fa-star text-2xl text-gray-400 cursor-pointer" data-value="1"></i>
                    <i class="fa-regular fa-star text-2xl text-gray-400 cursor-pointer" data-value="2"></i>
                    <i class="fa-regular fa-star text-2xl text-gray-400 cursor-pointer" data-value="3"></i>
                    <i class="fa-regular fa-star text-2xl text-gray-400 cursor-pointer" data-value="4"></i>
                    <i class="fa-regular fa-star text-2xl text-gray-400 cursor-pointer" data-value="5"></i>
                    <input type="hidden" name="rating" id="ratingValue" value="0">
                </div>
            </div>

            <div class="mb-4">
                <label for="comment" class="block text-gray-700 mb-2">Your Feedback</label>
                <textarea name="comment" id="comment" rows="4" required class="w-full p-2 border rounded-md" placeholder="Tell us what you liked or what could be improved..."></textarea>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="allow_public_display" class="mr-2">
                    <span class="text-sm text-gray-600">I agree to let my organization's name be shown with this review on the promotional page.</span>
                </label>
            </div>

            <div class="flex justify-end gap-4">
                <button type="button" id="closeReviewModal" class="py-2 px-4 bg-gray-200 rounded-lg">Cancel</button>
                <button type="submit" class="py-2 px-4 bg-[#005382] text-white rounded-lg">Submit Review</button>
            </div>
        </form>
         <div id="form-success" class="hidden text-center p-4 text-green-700">
            <p>Thank you for your feedback!</p>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Highlight active nav
    var currentLocation = window.location.href;
    document.querySelectorAll("nav a").forEach(function (link) {
        if (link.href === currentLocation) {
            link.classList.add("active");
        }
    });

    // Periodic unread refresh (optional guard to prevent errors when element missing)
    let contactsRefreshInterval = setInterval(refreshContacts, 7000);
    function refreshContacts() {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newChatNav = doc.getElementById('chatNav');
                const currentChatNav = document.getElementById('chatNav');
                if (newChatNav && currentChatNav) {
                    currentChatNav.innerHTML = newChatNav.innerHTML;
                }
            })
            .catch(err => console.error('Error refreshing contacts:', err));
    }

    // ----- Review Modal -----
    let modal = document.getElementById('reviewModal');
    let openBtn = document.getElementById('openReviewModal');
    let closeBtn = document.getElementById('closeReviewModal');
    let reviewForm = document.getElementById('reviewForm');
    let stars = document.querySelectorAll('#star-rating .fa-star');
    let ratingValue = document.getElementById('ratingValue');
    let successMessage = document.getElementById('form-success');

    if (!openBtn || !modal) {
        console.warn('Review modal elements not found in DOM.');
        return;
    }

    openBtn.addEventListener('click', function (e) {
        e.preventDefault();
        modal.classList.remove('hidden');
        reviewForm.reset();
        successMessage.classList.add('hidden');
        reviewForm.style.display = 'block';
        resetStars();
    });

    closeBtn.addEventListener('click', function () {
        modal.classList.add('hidden');
    });

    modal.addEventListener('click', function (e) {
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
        star.addEventListener('click', function () {
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
