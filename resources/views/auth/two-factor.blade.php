<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Two-Factor Authentication</title>
</head>
<body class="flex items-center justify-center min-h-screen h-screen p-5 lg:p-0">
    <div class="flex flex-col lg:flex-row shadow-lg rounded-lg bg-white w-full lg:max-w-3xl max-h-screen">
        <div class="flex flex-col gap-1 w-full lg:w-1/2 p-6 md:p-10">
            <h1 class="font-bold text-sm flex items-center gap-2 text-[#005382]">
                <img src="{{ asset('image/Logolandingpage.png') }}" alt="logo" class="w-10">RCT MED PHARMA
            </h1>

            <h1 class="text-center mt-12 font-medium tracking-wide text-lg md:text-2xl">Secure Your Login with Two-Factor Authentication</h1>
            <h1 class="text-sm md:text-lg text-center text-[#005382]/85">Enter the 6-digit code sent to you.</h1>

            <form method="POST" action="{{ route('2fa.check') }}" class="mt-10 relative">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-200 text-red-700 p-3 rounded-lg">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>⚠️ {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('message'))
                    <div class="bg-green-100 text-green-800 p-3 rounded-lg text-sm">
                        {{ session('message') }}
                    </div>
                @endif
                
                <div class="text-center md:text-center -mt-4 md:-mt-0">
                    <label for="two_factor_code" class="text-lg text-black/80 font-medium">Verification Code:</label>

                    <input type="hidden" name="two_factor_code" id="two_factor_code" placeholder="Enter 6-Digit Code">
                    @php
                        $inputCSS = "border border-gray-300 bg-white w-40 h-15 md:w-20 md:h-20 lg:w-14 lg:h-14 rounded-lg outline-none mt-2 md:text-xl lg:text-lg text-center TwoFA_num_inputs";
                    @endphp
                    <div class="flex gap-3 flex-row justify-center flex-nowrap">
                        <input type="number" class="{{$inputCSS}}" id="one" min="0" max="9" placeholder="0" autofocus required pattern="\d*">
                        <input type="number" class="{{$inputCSS}}" id="two" min="0" max="9" placeholder="0" required pattern="\d*">
                        <input type="number" class="{{$inputCSS}}" id="three" min="0" max="9" placeholder="0" required pattern="\d*">
                        <input type="number" class="{{$inputCSS}}" id="four" min="0" max="9" placeholder="0" required pattern="\d*">
                        <input type="number" class="{{$inputCSS}}" id="five" min="0" max="9" placeholder="0" required pattern="\d*">
                        <input type="number" class="{{$inputCSS}}" id="six" min="0" max="9" placeholder="0" required pattern="\d*">
                    </div>
                </div>

                <button type="submit" 
                        class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5 cursor-pointer hover:bg-[#005382] hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-sm md:text-base">
                    Verify Code
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600 mb-4">Or, get a new code:</p>
                <div class="flex flex-row justify-center gap-4">
                    
                    <form method="POST" action="{{ route('2fa.resend') }}" class="">
                        @csrf
                        <button class="hover:cursor-pointer border-[1px] border-[#005382] p-2 rounded-lg hover:border-none hover:bg-[#005382] hover:text-white hover:-translate-y-1 transition-all duration-200">Resend Via Email</button>
                    </form>

                    <form method="POST" action="{{ route('two-factor.sms') }}" class="">
                        @csrf
                        <button class="hover:cursor-pointer border-[1px] border-[#005382] p-2 rounded-lg hover:border-none hover:bg-[#005382] hover:text-white hover:-translate-y-1 transition-all duration-200">Resend Via SMS</button>
                    </form>
                </div>
            </div>      
        </div>

        <div id="flip" class="hidden lg:block w-1/2 transform scale-x-[-1]">
            <img src="{{ asset('image/loginpagebg.png') }}" alt="bg" class="w-full h-full object-cover">
        </div>
    </div>

    <x-loader/>
    
</body>
</html>


<script>
// tinamad nako, niGPT ko nalang navigation logic :)
// SA ARAW NG FINAL DEFENSE BALIK MOTO SA DATI PARA MADALIAN SI KUYA MAG DEBUG
//eto yung dating input na full:

// <input type="number" name="two_factor_code" id="two_factor_code" placeholder="Enter 6-Digit Code" 
// class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2 text-sm"
// required autofocus>


document.addEventListener('DOMContentLoaded', () => {
  const codeInputs = Array.from(document.querySelectorAll('.TwoFA_num_inputs'));
  const hiddenInput = document.getElementById('two_factor_code');

  // safety: ensure hidden exists
  if (!hiddenInput || codeInputs.length === 0) return;

  codeInputs.forEach((input, idx) => {
    // make behavior consistent across browsers
    input.setAttribute('inputmode', 'numeric');
    input.setAttribute('pattern', '\\d*');
    input.setAttribute('maxlength', '1'); // helpful for text inputs; harmless for number

    // sanitize and handle typing / paste distribution
    input.addEventListener('input', (e) => {
      let val = input.value.replace(/\D/g, ''); // strip non-digits

      if (!val) {
        input.value = '';
        updateHidden();
        return;
      }

      // user pasted multiple digits into one box -> distribute across following inputs
      if (val.length > 1) {
        for (let i = 0; i < val.length && (idx + i) < codeInputs.length; i++) {
          codeInputs[idx + i].value = val.charAt(i);
        }
        const nextFocus = Math.min(codeInputs.length - 1, idx + val.length);
        codeInputs[nextFocus].focus();
        codeInputs[nextFocus].select();
      } else {
        // single digit typed
        input.value = val.charAt(0);
        if (idx < codeInputs.length - 1) {
          codeInputs[idx + 1].focus();
          codeInputs[idx + 1].select();
        }
      }

      updateHidden();
    });

    // keyboard navigation and blocking non-printable characters
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace') {
        if (input.value === '' && idx > 0) {
          // go back and clear previous
          const prev = codeInputs[idx - 1];
          prev.value = '';
          prev.focus();
          updateHidden();
          e.preventDefault();
        }
        // else let default clear current input
      } else if (e.key === 'ArrowLeft' && idx > 0) {
        codeInputs[idx - 1].focus();
        e.preventDefault();
      } else if (e.key === 'ArrowRight' && idx < codeInputs.length - 1) {
        codeInputs[idx + 1].focus();
        e.preventDefault();
      } else if (e.key.length === 1 && !/\d/.test(e.key)) {
        // block any printable non-digit
        e.preventDefault();
      }
    });

    // paste handling (explicit)
    input.addEventListener('paste', (e) => {
      e.preventDefault();
      const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
      for (let i = 0; i < paste.length && (idx + i) < codeInputs.length; i++) {
        codeInputs[idx + i].value = paste.charAt(i);
      }
      const focusIdx = Math.min(codeInputs.length - 1, idx + paste.length);
      codeInputs[focusIdx].focus();
      updateHidden();
    });
  });

  function updateHidden() {
    hiddenInput.value = codeInputs.map(i => i.value || '').join('');
  }
});
</script>