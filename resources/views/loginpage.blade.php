<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
  <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
  <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
  <title>Sample Login Page</title>
</head>
<style>
@import url('https://fonts.googleapis.com/css2?family=Great+Vibes&family=Leckerli+One&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
*{
  font-family: "Roboto", sans-serif;
}
body {
  position: relative;
  min-height: 100vh;
  margin: 0;
  overflow: hidden;
}

body::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: url('{{ asset('image/Image 62.png') }}');
  background-repeat: repeat;
  background-size: cover;
  background-position: center;
  filter: blur(8px);
  z-index: -1;
  background-color: rgba(0, 0, 0, 0.4);
  background-blend-mode: darken;
}
#flip{
    transform: scaleX(-1);
}

</style>
<body class="flex items-center justify-center h-screen p-10">

  <div class="flex flex-col lg:flex-row shadow-lg rounded-lg bg-white w-full lg:w-[55%] overflow-hidden">
    
    <div class="flex flex-col gap-2 w-full lg:w-1/2 p-6 md:p-10">
      <h1 class="font-bold text-sm flex items-center gap-2 text-[#005382]">
        <img src="{{ asset('image/Logolandingpage.png') }}" alt="logo" class="w-10">RCT MED PHARMA
      </h1>

      <h1 class="text-center mt-12 font-medium tracking-wide text-lg md:text-2xl">Sign in to your Account</h1>
      <h1 class="text-sm md:text-lg text-center text-[#005382]/85">Manage your Medication Effortlessly Anytime, Anywhere</h1>

      <form action="#" class="mt-10 space-y-5">
        <div>
          <label for="email" class="text-xs text-black/80 font-medium">Email Address:</label>
          <input type="email" name="email" id="email" placeholder="Enter Your Email" 
                 class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2 text-sm">
        </div>

        <div>
          <div class="flex justify-between items-center">
            <label for="password" class="text-xs text-black/80 font-medium">Password:</label>
            <a href="#" class="text-xs text-[#005382] font-semibold">Forgot Password?</a>
          </div>
          <input type="password" name="password" id="password" placeholder="Enter Your Password" 
                 class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2 text-sm">
        </div>

        <div class="flex items-center mt-2 gap-2">
          <input type="checkbox" name="remember" class="w-4 h-4">
          <label for="remember" class="text-xs text-black/80 font-medium">Remember Me</label>
        </div>

        <button type="submit" 
          class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5 cursor-pointer hover:bg-[#005382] hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-sm md:text-base">
          Sign in
        </button>
      </form>
    </div>

    <div id="flip" class="hidden lg:block w-1/2">
      <img src="{{ asset('image/loginpagebg.png') }}" alt="bg" class="w-full h-full object-cover">
    </div>
  </div>
  
</body>
</html>
