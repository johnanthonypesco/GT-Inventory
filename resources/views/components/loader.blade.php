<style>
	.loader div {
		width: 2rem;
		height: 2rem;
		gap: 20px;
		background: #3490dc;
		border-radius:5px;
		animation: loader 0.6s linear infinite;
	}
	.loader div:nth-child(2) {
		animation-delay: 0.2s;
	}
	.loader div:nth-child(3) {
		animation-delay: 0.4s;
	}
	@keyframes loader {
		0% {
			transform: translateY(0);
			background: #3490dc;
		}
		50% {
			transform: translateY(-1rem);
			background: #6574cd;
		}
		100% {
			transform: translateY(0);
			background: #3490dc;
		}
	}
</style>

<div class="bg-white fixed top-0 left-0 w-full h-full flex justify-center items-center z-50 loader gap-5" id="loader">
	<div></div>
	<div></div>
	<div></div>
</div>

<script>
    const loader = document.getElementById('loader');
    window.addEventListener('load', () => {
		const main = document.querySelector('main');
		const navbar = document.querySelector('#sidebar');
		const customernavbar = document.querySelector('#customernavbar');
        setTimeout(() => {
            loader.style.display = 'none';
			main.classList.remove('opacity-0');
			navbar.classList.remove('opacity-0');
			customernavbar.classList.remove('opacity-0');
        }, 1000);
    });
</script>