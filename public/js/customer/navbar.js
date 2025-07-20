var currentLocation = window.location.href;
var navLinks = document.querySelectorAll("nav a");
navLinks.forEach(function (link) {
    if (link.href === currentLocation) {
        link.classList.add("active");
    }
});
// add auto reload for realtime
document.addEventListener('DOMContentLoaded', function() {
    let contactsRefreshInterval;

    // Start contacts refresh interval
    function startContactsRefresh() {
        contactsRefreshInterval = setInterval(refreshContacts, 7000); // Refresh every 6 seconds
    }

    // Stop contacts refresh interval
    function stopContactsRefresh() {
        clearInterval(contactsRefreshInterval);
    }

    // Function to refresh contacts list
    function refreshContacts() {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContactsList = doc.getElementById('chatNav');
                if (newContactsList) {
                    const currentContactsList = document.getElementById('chatNav');
                    currentContactsList.innerHTML = newContactsList.innerHTML;
                }
            })
            .catch(error => console.error('Error refreshing contacts:', error));
    }

    // Start the refresh interval when the page loads
    startContactsRefresh();


     // NEW SCRIPT FOR REVIEW MODAL
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('reviewModal');
        const openBtn = document.getElementById('openReviewModal');
        const closeBtn = document.getElementById('closeReviewModal');
        const reviewForm = document.getElementById('reviewForm');
        const stars = document.querySelectorAll('#star-rating .fa-star');
        const ratingValue = document.getElementById('ratingValue');
        const successMessage = document.getElementById('form-success');

        // Open modal
        openBtn.addEventListener('click', (e) => {
            e.preventDefault();
            modal.classList.remove('hidden');
            reviewForm.reset();
            successMessage.classList.add('hidden');
            reviewForm.style.display = 'block';
            resetStars();
        });

        // Close modal
        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });

        // Star rating logic
        function resetStars() {
            stars.forEach(star => {
                star.classList.replace('fa-solid', 'fa-regular');
                star.classList.remove('text-yellow-400');
            });
        }

        stars.forEach(star => {
            star.addEventListener('click', () => {
                const value = parseInt(star.getAttribute('data-value'));
                ratingValue.value = value;
                resetStars();
                for (let i = 0; i < value; i++) {
                    stars[i].classList.replace('fa-regular', 'fa-solid');
                    stars[i].classList.add('text-yellow-400');
                }
            });
        });

        // Handle form submission with Fetch API
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('customer.review.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    reviewForm.style.display = 'none';
                    successMessage.classList.remove('hidden');

                    // Hide modal after 2 seconds
                    setTimeout(() => {
                       modal.classList.add('hidden');
                    }, 2000);
                } else {
                    // Handle potential validation errors here
                    alert('Something went wrong. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
});
