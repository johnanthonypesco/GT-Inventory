@php
    // This script resets the card's style when the mouse leaves. It's defined here for cleanliness.
    $onMouseLeaveScript = "const el=this;el.style.transition='all .7s cubic-bezier(.25,.8,.25,1)',el.style.transform='translateY(0) rotateX(0deg) rotateY(0deg) scale(1)',el.style.boxShadow='0 1px 3px rgba(0,0,0,0.1),0 2px 6px rgba(0,0,0,0.08),0 4px 8px rgba(0,0,0,0.06)';setTimeout(()=>{el.style.transition.includes('cubic-bezier')&&(el.style.transition='')},700); const glow = el.querySelector('.tilt-glow'); if(glow) el.removeChild(glow);";
@endphp
<div {{ isset($cardId) ? 'id='.$cardId : '' }} class="bg-white rounded-xl p-4 flex items-center transform-gpu transition-all duration-500 ease-out relative overflow-hidden will-change-transform {{ $extraClasses ?? '' }}"
     style="transform-style: preserve-3d; perspective: 1200px; transform: translateZ(0); box-shadow: 0 5px 8px rgba(0, 0, 0, 0.389)"
     onmousemove="
       const el=this,
             rect=el.getBoundingClientRect(),
             x=event.clientX-rect.left,
             y=event.clientY-rect.top,
             tiltX=(y-rect.height/2)/rect.height*10,
             tiltY=-((x-rect.width/2)/rect.width)*10;
       el.style.transform=`translateY(-12px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) scale(1.03)`;
       el.style.boxShadow=`0 4px 12px rgba(0,0,0,0.08),0 ${20+Math.abs(tiltX)*2}px 30px rgba(0,0,0,0.12),0 0 0 1px #e5e7eb`;
       let glow = el.querySelector('.tilt-glow');
       if(!glow){
           glow=document.createElement('div');
           glow.classList.add('tilt-glow');
           glow.style.cssText=`position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;border-radius:inherit`;
           el.appendChild(glow);
       }
       glow.style.background=`radial-gradient(circle at ${x}px ${y}px, {{ $glowColor ?? 'rgba(96,165,250,.15)' }} 0%,transparent 60%)`;
     "
     onmouseleave="{{ $onMouseLeaveScript }}">

    <div class="flex-1 min-w-0 relative">
        <h3 id="{{ $countId }}-title" class="text-gray-500 font-medium text-sm truncate">{{ $title }}</h3>
        <p id="{{ $countId }}" class="text-gray-800 text-2xl font-bold mt-1 break-all">{!! $count !!}</p>
    </div>

    <div class="ml-4 relative">
        <div class="w-12 h-12 flex items-center justify-center {{ $bgColor }} rounded-full">
            <img src="{{ asset('image/' . $icon) }}" alt="{{ $title }}" class="h-6 w-6">
        </div>
    </div>
</div>