<section id="loader" class="fixed w-full h-screen top-0 left-0 bg-white flex gap-4 justify-center items-center z-10">
    <div class="box w-[30px] h-[30px] bg-blue-600 rounded-md"></div>
    <div class="box w-[30px] h-[30px] bg-blue-600 rounded-md"></div>
    <div class="box w-[30px] h-[30px] bg-blue-600 rounded-md"></div>
</section>

<script>
    const loader = document.getElementById('loader');
    window.addEventListener('load', () => {
        setTimeout(() => {
            loader.style.display = 'none';
        }, 1500);
    });
</script>