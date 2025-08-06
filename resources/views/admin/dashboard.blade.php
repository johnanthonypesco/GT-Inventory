<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css" rel="stylesheet">
    <link href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <title>Dashboard</title>
    <style>
        /* Styles for the AI Analysis Section */
        /* Styles for the AI Analysis Section */
        .anomaly-item.positive { border-left-color: #16a34a; }
        .anomaly-item.negative { border-left-color: #dc2626; }
        .anomaly-item.warning { border-left-color: #f59e0b; }
        .recommendation-card { background: linear-gradient(135deg, #6d28d9, #4f46e5); }
        .loader { display: flex; align-items: center; justify-content: center; padding: 2rem; color: #6b21a8; }
        .loader svg { color: #6b21a8; }

        /* Chart-related styles */
        .chart-container { transition: all 0.3s ease; min-height: 300px; cursor: grab; }
        .chart-container:active { cursor: grabbing; }

        /* AI History Panel Styles */
        .history-panel { max-height: 300px; overflow-y: auto; }
        .history-item { cursor: pointer; transition: background-color 0.2s ease-in-out; }
        .history-item:hover { background-color: #f3e8ff; }
        .history-item.active { background-color: #e9d5ff; border-left-color: #9333ea; }
        #refreshAiBtn:disabled {
        cursor: not-allowed;
    }
    </style>
</head>
<body class="flex flex-col md:flex-row gap-4 mx-auto">
    <x-admin.navbar/>

    <main class="md:w-full h-full lg:ml-[16%]">
        <x-admin.header title="Dashboard" icon="fa-solid fa-gauge" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- Start of the cards container --}}
        {{-- Start of the cards container --}}
        <div class="h-full mt-5 overflow-y-auto bg-gray-50 p-4 rounded-lg shadow-md">

            {{-- DYNAMIC CARD COUNT LOGIC --}}
            @php
                $cardCount = 3; 
                if (!$currentUser instanceof \App\Models\Staff) { $cardCount++; }
                $cardCount++;
            @endphp

            {{-- UPDATED GRID WITH ANIMATED CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-{{ $cardCount }} gap-4 items-stretch">

                {{-- Card 1: Total Delivered --}}
                <div class="bg-white rounded-xl p-4 md:p-5 flex items-center h-full transform-gpu transition-all duration-500 ease-out relative overflow-hidden will-change-transform"
                     style="transform-style: preserve-3d; perspective: 1200px; transform: translateZ(0);"
                     onmousemove="const el=this,rect=el.getBoundingClientRect(),x=event.clientX-rect.left,y=event.clientY-rect.top,tiltX=(y-rect.height/2)/rect.height*10,tiltY=-((x-rect.width/2)/rect.width)*10;el.style.transform=`translateY(-12px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) scale(1.03)`;el.style.boxShadow=`0 4px 12px rgba(0,0,0,0.08),0 ${20+Math.abs(tiltX)*2}px 30px rgba(0,0,0,0.12),0 0 0 1px #e5e7eb`;if(!el.querySelector('.tilt-glow')){const t=document.createElement('div');t.classList.add('tilt-glow'),t.style.cssText=`position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;background:radial-gradient(circle at ${x}px ${y}px,rgba(96,165,250,.15) 0%,transparent 60%);border-radius:inherit;z-index:0`,el.appendChild(t)}else el.querySelector('.tilt-glow').style.background=`radial-gradient(circle at ${x}px ${y}px,rgba(96,165,250,.15) 0%,transparent 60%)`"
                     onmouseleave="const el=this;el.style.transition='all .7s cubic-bezier(.25,.8,.25,1)',el.style.transform='translateY(0) rotateX(0deg) rotateY(0deg) scale(1)',el.style.boxShadow='0 1px 3px rgba(0,0,0,0.1),0 2px 6px rgba(0,0,0,0.08),0 4px 8px rgba(0,0,0,0.06)';setTimeout(()=>{el.style.transition.includes('cubic-bezier')&&(el.style.transition='')},700),el.querySelector('.tilt-glow')&&el.removeChild(el.querySelector('.tilt-glow'))">
                    <div class="flex-1 min-w-0 relative z-10">
                        <h3 class="text-gray-500 font-medium text-sm md:text-base truncate">Total Delivered</h3>
                        <p class="text-gray-800 text-2xl md:text-3xl font-bold mt-1 break-all">{{ $totalOrders }}</p>
                    </div>
                    <div class="ml-4 relative z-10">
                        <div class="w-12 h-12 md:w-14 md:h-14 flex items-center justify-center bg-blue-100 rounded-full">
                            <img src="{{ asset('image/complete.png') }}" alt="Total Delivered" class="h-6 w-6 md:h-7 md:h-7">
                        </div>
                    </div>
                </div>

                {{-- Card 2: Pending Orders --}}
                <div class="bg-white rounded-xl p-4 md:p-5 flex items-center h-full transform-gpu transition-all duration-500 ease-out relative overflow-hidden will-change-transform"
                     style="transform-style: preserve-3d; perspective: 1200px; transform: translateZ(0);"
                     onmousemove="const el=this,rect=el.getBoundingClientRect(),x=event.clientX-rect.left,y=event.clientY-rect.top,tiltX=(y-rect.height/2)/rect.height*10,tiltY=-((x-rect.width/2)/rect.width)*10;el.style.transform=`translateY(-12px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) scale(1.03)`;el.style.boxShadow=`0 4px 12px rgba(0,0,0,0.08),0 ${20+Math.abs(tiltX)*2}px 30px rgba(0,0,0,0.12),0 0 0 1px #e5e7eb`;if(!el.querySelector('.tilt-glow')){const t=document.createElement('div');t.classList.add('tilt-glow'),t.style.cssText=`position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;background:radial-gradient(circle at ${x}px ${y}px,rgba(252,211,77,.15) 0%,transparent 60%);border-radius:inherit;z-index:0`,el.appendChild(t)}else el.querySelector('.tilt-glow').style.background=`radial-gradient(circle at ${x}px ${y}px,rgba(252,211,77,.15) 0%,transparent 60%)`"
                     onmouseleave="const el=this;el.style.transition='all .7s cubic-bezier(.25,.8,.25,1)',el.style.transform='translateY(0) rotateX(0deg) rotateY(0deg) scale(1)',el.style.boxShadow='0 1px 3px rgba(0,0,0,0.1),0 2px 6px rgba(0,0,0,0.08),0 4px 8px rgba(0,0,0,0.06)';setTimeout(()=>{el.style.transition.includes('cubic-bezier')&&(el.style.transition='')},700),el.querySelector('.tilt-glow')&&el.removeChild(el.querySelector('.tilt-glow'))">
                    <div class="flex-1 min-w-0 relative z-10">
                        <h3 class="text-gray-500 font-medium text-sm md:text-base truncate">Pending Orders</h3>
                        <p class="text-gray-800 text-2xl md:text-3xl font-bold mt-1 break-all">{{ $pendingOrders }}</p>
                    </div>
                    <div class="ml-4 relative z-10">
                        <div class="w-12 h-12 md:w-14 md:h-14 flex items-center justify-center bg-yellow-100 rounded-full">
                            <img src="{{ asset('image/pending.png') }}" alt="Pending Orders" class="h-6 w-6 md:h-7 md:h-7">
                        </div>
                    </div>
                </div>

                {{-- Card 3: Cancelled Orders --}}
                <div class="bg-white rounded-xl p-4 md:p-5 flex items-center h-full transform-gpu transition-all duration-500 ease-out relative overflow-hidden will-change-transform"
                     style="transform-style: preserve-3d; perspective: 1200px; transform: translateZ(0);"
                     onmousemove="const el=this,rect=el.getBoundingClientRect(),x=event.clientX-rect.left,y=event.clientY-rect.top,tiltX=(y-rect.height/2)/rect.height*10,tiltY=-((x-rect.width/2)/rect.width)*10;el.style.transform=`translateY(-12px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) scale(1.03)`;el.style.boxShadow=`0 4px 12px rgba(0,0,0,0.08),0 ${20+Math.abs(tiltX)*2}px 30px rgba(0,0,0,0.12),0 0 0 1px #e5e7eb`;if(!el.querySelector('.tilt-glow')){const t=document.createElement('div');t.classList.add('tilt-glow'),t.style.cssText=`position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;background:radial-gradient(circle at ${x}px ${y}px,rgba(239,68,68,.1) 0%,transparent 60%);border-radius:inherit;z-index:0`,el.appendChild(t)}else el.querySelector('.tilt-glow').style.background=`radial-gradient(circle at ${x}px ${y}px,rgba(239,68,68,.1) 0%,transparent 60%)`"
                     onmouseleave="const el=this;el.style.transition='all .7s cubic-bezier(.25,.8,.25,1)',el.style.transform='translateY(0) rotateX(0deg) rotateY(0deg) scale(1)',el.style.boxShadow='0 1px 3px rgba(0,0,0,0.1),0 2px 6px rgba(0,0,0,0.08),0 4px 8px rgba(0,0,0,0.06)';setTimeout(()=>{el.style.transition.includes('cubic-bezier')&&(el.style.transition='')},700),el.querySelector('.tilt-glow')&&el.removeChild(el.querySelector('.tilt-glow'))">
                    <div class="flex-1 min-w-0 relative z-10">
                        <h3 class="text-gray-500 font-medium text-sm md:text-base truncate">Cancelled Orders</h3>
                        <p class="text-gray-800 text-2xl md:text-3xl font-bold mt-1 break-all">{{ $cancelledOrders }}</p>
                    </div>
                    <div class="ml-4 relative z-10">
                        <div class="w-12 h-12 md:w-14 md:h-14 flex items-center justify-center bg-red-100 rounded-full">
                            <img src="{{ asset('image/cancel.png') }}" alt="Cancelled Orders" class="h-6 w-6 md:h-7 ">
                        </div>
                    </div>
                </div>

                @if(!$currentUser instanceof \App\Models\Staff)
                {{-- Card 4: Total Sale --}}
                <div id="totalSaleCard" class="bg-white rounded-xl p-4 md:p-5 flex items-center h-full cursor-pointer transform-gpu transition-all duration-500 ease-out relative overflow-hidden will-change-transform"
                     style="transform-style: preserve-3d; perspective: 1200px; transform: translateZ(0);"
                     onmousemove="const el=this,rect=el.getBoundingClientRect(),x=event.clientX-rect.left,y=event.clientY-rect.top,tiltX=(y-rect.height/2)/rect.height*10,tiltY=-((x-rect.width/2)/rect.width)*10;el.style.transform=`translateY(-12px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) scale(1.03)`;el.style.boxShadow=`0 4px 12px rgba(0,0,0,0.08),0 ${20+Math.abs(tiltX)*2}px 30px rgba(0,0,0,0.12),0 0 0 1px #e5e7eb`;if(!el.querySelector('.tilt-glow')){const t=document.createElement('div');t.classList.add('tilt-glow'),t.style.cssText=`position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;background:radial-gradient(circle at ${x}px ${y}px,rgba(59,130,246,.15) 0%,transparent 60%);border-radius:inherit;z-index:0`,el.appendChild(t)}else el.querySelector('.tilt-glow').style.background=`radial-gradient(circle at ${x}px ${y}px,rgba(59,130,246,.15) 0%,transparent 60%)`"
                     onmouseleave="const el=this;el.style.transition='all .7s cubic-bezier(.25,.8,.25,1)',el.style.transform='translateY(0) rotateX(0deg) rotateY(0deg) scale(1)',el.style.boxShadow='0 1px 3px rgba(0,0,0,0.1),0 2px 6px rgba(0,0,0,0.08),0 4px 8px rgba(0,0,0,0.06)';setTimeout(()=>{el.style.transition.includes('cubic-bezier')&&(el.style.transition='')},700),el.querySelector('.tilt-glow')&&el.removeChild(el.querySelector('.tilt-glow'))">
                    <div class="flex-1 min-w-0 relative z-10">
                        <h3 class="text-gray-500 font-medium text-sm md:text-base truncate">Total Sale</h3>
                        <p id="totalSaleValue" class="text-gray-800 text-2xl md:text-2xl sm:text-lg font-bold mt-1 break-all">₱{{ number_format($totalRevenue, 0) }}</p>
                    </div>
                    <div class="ml-4 relative z-10">
                        <div class="w-12 h-12 md:w-14 md:h-14 flex items-center justify-center bg-green-100 rounded-full">
                            <img src="{{ asset('image/pera.png') }}" alt="Total Revenue" class="h-6 w-6 md:h-7">
                        </div>
                    </div>
                </div>
                @endif
                
                {{-- Card 5: Unread Messages --}}
                <div class="bg-white rounded-xl p-4 md:p-5 flex items-center h-full transform-gpu transition-all duration-500 ease-out relative overflow-hidden will-change-transform"
                     style="transform-style: preserve-3d; perspective: 1200px; transform: translateZ(0);"
                     onmousemove="const el=this,rect=el.getBoundingClientRect(),x=event.clientX-rect.left,y=event.clientY-rect.top,tiltX=(y-rect.height/2)/rect.height*10,tiltY=-((x-rect.width/2)/rect.width)*10;el.style.transform=`translateY(-12px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) scale(1.03)`;el.style.boxShadow=`0 4px 12px rgba(0,0,0,0.08),0 ${20+Math.abs(tiltX)*2}px 30px rgba(0,0,0,0.12),0 0 0 1px #e5e7eb`;if(!el.querySelector('.tilt-glow')){const t=document.createElement('div');t.classList.add('tilt-glow'),t.style.cssText=`position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;background:radial-gradient(circle at ${x}px ${y}px,rgba(167,139,250,.15) 0%,transparent 60%);border-radius:inherit;z-index:0`,el.appendChild(t)}else el.querySelector('.tilt-glow').style.background=`radial-gradient(circle at ${x}px ${y}px,rgba(167,139,250,.15) 0%,transparent 60%)`"
                     onmouseleave="const el=this;el.style.transition='all .7s cubic-bezier(.25,.8,.25,1)',el.style.transform='translateY(0) rotateX(0deg) rotateY(0deg) scale(1)',el.style.boxShadow='0 1px 3px rgba(0,0,0,0.1),0 2px 6px rgba(0,0,0,0.08),0 4px 8px rgba(0,0,0,0.06)';setTimeout(()=>{el.style.transition.includes('cubic-bezier')&&(el.style.transition='')},700),el.querySelector('.tilt-glow')&&el.removeChild(el.querySelector('.tilt-glow'))">
                    <div class="flex-1 min-w-0 relative z-10">
                        <h3 class="text-gray-500 font-medium text-sm md:text-base truncate">Unread Messages</h3>
                        <p class="text-gray-800 text-2xl md:text-3xl font-bold mt-1 break-all">
                            @if($currentUser instanceof \App\Models\SuperAdmin)
                                {{ $unreadMessagesSuperAdmin ?? 0 }}
                            @elseif($currentUser instanceof \App\Models\Admin)
                                {{ $unreadMessagesAdmin ?? 0 }}
                            @elseif($currentUser instanceof \App\Models\Staff)
                                {{ $unreadMessagesStaff ?? 0 }}
                            @endif
                        </p>
                    </div>
                    <div class="ml-4 relative z-10">
                        <div class="w-12 h-12 md:w-14 md:h-14 flex items-center justify-center bg-purple-100 rounded-full">
                            <img src="{{ asset('image/messages.png') }}" alt="Unread Messages" class="h-6 w-6 md:h-7">
                        </div>
                    </div>
                </div>

            </div>

            {{-- *** START: MODIFIED AI ANALYSIS SECTION *** --}}
            @if(!$currentUser instanceof \App\Models\Staff && !$currentUser instanceof \App\Models\Admin)
            <div id="ai-analysis-section" class="mt-6 bg-white p-4 rounded-lg shadow-md border-t-4 border-purple-600">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-4 gap-3 sm:gap-4">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-brain mr-3 text-purple-600"></i>
                        AI-Powered Analysis
                    </h2>
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                        
                        <select id="aiModelSelect" class="w-full sm:w-auto p-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
                            <option value="primary:deepseek/deepseek-r1-0528:free" selected>DeepSeek R1</option>
                            <option value="secondary:tencent/hunyuan-a13b-instruct:free">Tencent Hunyuan</option>
                        </select>
                        
                        <button id="refreshAiBtn" class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                            <i class="fas fa-redo-alt"></i> Refresh
                        </button>
                        <button id="viewAiHistoryBtn" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <i class="fas fa-history"></i> History
                        </button>
                        <div id="ai-timer-container" class="text-sm text-gray-500 font-medium mt-2 sm:mt-0" style="display: none;">
                            <i class="far fa-clock mr-1"></i>
                            <span id="ai-timer"></span>
                        </div>
                    </div>
                </div>

                <div id="ai-history-panel" class="hidden my-4 p-3 bg-gray-50 rounded-lg border history-panel">
                    <h4 class="font-semibold text-gray-700 mb-2">Analysis History</h4>
                    <div id="ai-history-list" class="space-y-2"></div>
                </div>

                <hr class="my-4 border-gray-200">

                <div id="ai-content-wrapper" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-3"><i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>AI Anomaly Detection</h3>
                            <div id="ai-anomalies-content" class="space-y-3">
                                <div class="loader">
                                    <svg class="animate-spin -ml-1 mr-3 h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="text-lg font-semibold">Loading AI analysis...</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-3"><i class="fas fa-lightbulb mr-2 text-blue-500"></i>AI Recommendations</h3>
                            <div id="ai-recommendations-content" class="space-y-3"></div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-3"><i class="fas fa-chart-pie mr-2 text-purple-500"></i>Chart Analysis</h3>
                        <div id="ai-chart-analysis-content" class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 min-h-[80px] flex flex-col items-center justify-center text-center"></div>
                        <div class="flex justify-end mt-3">
                            <button id="speakAnalysisBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 hidden">
                                <i class="fas fa-volume-up mr-2"></i> Speak Chart Analysis
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            {{-- *** END: MODIFIED AI ANALYSIS SECTION *** --}}


            @if(!$currentUser instanceof \App\Models\Staff)
                <div class="mt-5 bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-3">Low Stock Alerts</h3>
                    <ul>
                        @forelse($lowStockProducts as $product)
                            <li class="text-red-600">{{ $product->generic_name }} - {{ $product->total_quantity }} units left</li>
                        @empty
                            <li class="text-green-600">No low stock products.</li>
                        @endforelse
                    </ul>
                </div>
            @endif

            @if(!$currentUser instanceof \App\Models\Staff)
            <div class="mt-5 bg-white p-4 rounded-lg shadow-md">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <h3 class="text-lg font-semibold text-gray-800">Chart Display Options</h3>
                    <div class="flex-1 flex flex-col gap-4">
                        <select id="chartFilter" class="p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="all">Show All Charts</option>
                            <option value="revenue">Revenue Chart Only</option>
                            <option value="deductions">Products Delivered Only</option>
                            <option value="performance">Product Performance Only</option>
                            <option value="inventory">Inventory Levels Only</option>
                            <option value="trends">Product Trends & Predictions Only</option>
                            <option value="custom">Custom Selection</option>
                        </select>

                        <div id="customChartSelection" class="hidden w-full bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div class="flex flex-col gap-3">
                                <h4 class="text-sm font-medium text-gray-700">Select Charts to Display:</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                        <input type="checkbox" name="customChart" value="revenue" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-gray-700">Revenue</span>
                                    </label>
                                    <label class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                        <input type="checkbox" name="customChart" value="deductions" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-gray-700">Products Delivered</span>
                                    </label>
                                    <label class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                        <input type="checkbox" name="customChart" value="performance" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-gray-700">Product Performance</span>
                                    </label>
                                    <label class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                        <input type="checkbox" name="customChart" value="inventory" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-gray-700">Inventory Levels</span>
                                    </label>
                                    <label class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                        <input type="checkbox" name="customChart" value="trends" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-gray-700">Product Trends & Predictions</span>
                                    </label>
                                </div>
                                <button id="applyCustomCharts" class="self-end mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Apply Selection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif


            @if(!$currentUser instanceof \App\Models\Staff)
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6" id="chartsContainer">
                <div class="space-y-4 md:space-y-6" id="leftCharts">
                    <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 revenue-chart" data-chart-id="revenue">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 md:mb-4 gap-2">
                            <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800">Revenue Over Time</h3>
                            <span class="text-xs sm:text-sm text-gray-500">Delivered Orders (by Order Date)</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4 mb-4 md:mb-6">
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Time Period</label>
                                <select id="revenueTimePeriod" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="day">Daily</option>
                                    <option value="week">Weekly</option>
                                    <option value="month" selected>Monthly</option>
                                    <option value="year">Yearly</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Year</label>
                                <select id="revenueYearFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach(range(date('Y'), date('Y') - 5, -1) as $year)
                                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="revenueMonthContainer">
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Month</label>
                                <select id="revenueMonthFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach(range(1, 12) as $month)
                                        <option value="{{ $month }}" {{ $month == date('n') ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $month, 10)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="revenueWeekContainer" class="hidden">
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Week</label>
                                <select id="revenueWeekFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></select>
                            </div>
                            <div class="flex items-end">
                                <button id="revenueUpdateBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-1.5 sm:py-2 px-3 rounded-lg text-xs sm:text-sm transition-colors">
                                    Update Chart
                                </button>
                            </div>
                        </div>
                        <div class="h-60 xs:h-64 sm:h-72 md:h-80 bg-white rounded-lg chart-canvas" id="revenueChartContainer"></div>
                    </div>

                    <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 deductions-chart" data-chart-id="deductions">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 md:mb-4 gap-2">
                            <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800">Products Delivered (Top 10)</h3>
                            <span class="text-xs sm:text-sm text-gray-500">Delivered Orders</span>
                        </div>
                        <div class="grid grid-cols-3 gap-3 sm:gap-4 mb-4 md:mb-6">
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Year</label>
                                <select id="deductedYearFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach(range(date('Y'), date('Y') - 5, -1) as $year)
                                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Month</label>
                                <select id="deductedMonthFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Location</label>
                                <select id="deductedLocationFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Locations</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->province }}">{{ $location->city }}, {{ $location->province }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="h-60 xs:h-64 sm:h-72 md:h-80 chart-canvas" id="deductedQuantitiesChartContainer"></div>
                    </div>

                    <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 inventory-chart" data-chart-id="inventory">
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Inventory Levels (Top 10 Low Stock)</h3>
                        <div class="grid grid-cols-1 max-w-xs gap-3 sm:gap-4 mb-4 md:mb-6">
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Filter by Location</label>
                                <select id="inventoryLocationFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Locations</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->city }}, {{ $location->province }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="h-60 xs:h-64 sm:h-72 md:h-80 chart-canvas" id="inventoryLevelsChartContainer"></div>
                    </div>
                </div>

                <div class="space-y-4 md:space-y-6" id="rightCharts">
                    <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 trends-chart" data-chart-id="trends">
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Product Trends & Predictions</h3>
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4 md:mb-6">
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Filter by Season</label>
                                <select id="seasonFilter" class="w-full p-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="all">All Seasons</option>
                                    <option value="tag-init">Summer Season</option>
                                    <option value="tag-ulan">Rainy Season</option>
                                    <option value="all-year">All Year Products</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Year</label>
                                <select id="trendYearFilter" class="w-full p-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    @foreach(range(date('Y'), date('Y') - 2, -1) as $year)
                                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="h-64 chart-canvas" id="seasonalTrendsChartContainer"></div>

                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-3">Next Month's Predicted Top Products</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="predictionCardsContainer"></div>
                        </div>
                    </div>
                    <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 performance-chart" data-chart-id="performance">
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Ordered Products Performance</h3>
                        <div class="flex flex-wrap gap-2 mb-4 md:mb-6">
                            <button id="mostSoldBtn" class="px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm rounded-full font-medium transition-colors bg-blue-100 text-blue-700 hover:bg-blue-200">Most Ordered</button>
                            <button id="moderateSoldBtn" class="px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm rounded-full font-medium transition-colors bg-emerald-100 text-emerald-700 hover:bg-emerald-200">Moderate Ordered</button>
                            <button id="lowSoldBtn" class="px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm rounded-full font-medium transition-colors bg-amber-100 text-amber-700 hover:bg-amber-200">Low Ordered</button>
                        </div>
                        <div class="h-72 sm:h-80 md:h-96 chart-canvas" id="productPerformanceChartContainer"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div id="revenueFilterModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md mx-4 border border-gray-100">
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <h3 class="text-xl font-bold text-gray-900">Filter Total Sales</h3>
                        <p class="text-sm text-gray-500">Select your preferred date range</p>
                    </div>
                    <button id="closeRevenueModalBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                </div>
                <div class="mt-6 space-y-5">
                    <div>
                        <label for="revenueModalPeriod" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                        <select id="revenueModalPeriod" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <option value="all_time" selected>All Time</option>
                            <option value="7d">Last 7 Days</option>
                            <option value="today">Today</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="this_year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div id="revenueModalCustomRange" class="hidden space-y-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="space-y-2">
                            <label for="revenueModalStartDate" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" id="revenueModalStartDate" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label for="revenueModalEndDate" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" id="revenueModalEndDate" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-3">
                    <button id="cancelRevenueFilterBtn" class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                    <button id="applyRevenueFilterBtn" class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Apply Filter</button>
                </div>
            </div>
        </div>
    </main>


    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    const chartInstances = {};
    let sortableInstances = {}; // To hold the SortableJS instances
    const XL_BREAKPOINT = 1280; // Tailwind CSS 'xl' breakpoint in pixels

    // --- SCRIPT FOR REVENUE CARD MODAL ---
    const totalSaleCard = document.getElementById('totalSaleCard');
    const revenueModal = document.getElementById('revenueFilterModal');
    const closeModalBtn = document.getElementById('closeRevenueModalBtn');
    const applyFilterBtn = document.getElementById('applyRevenueFilterBtn');
    const totalSaleValueEl = document.getElementById('totalSaleValue');
    const modalPeriodSelect = document.getElementById('revenueModalPeriod');
    const modalCustomRangeContainer = document.getElementById('revenueModalCustomRange');

    if (totalSaleCard) {
        totalSaleCard.addEventListener('click', () => {
            revenueModal.classList.remove('hidden');
        });
    }
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => {
            revenueModal.classList.add('hidden');
        });
    }
    if (revenueModal) {
        revenueModal.addEventListener('click', (event) => {
            if (event.target === revenueModal) {
                revenueModal.classList.add('hidden');
            }
        });
    }
    if (modalPeriodSelect) {
        modalPeriodSelect.addEventListener('change', () => {
            modalCustomRangeContainer.classList.toggle('hidden', modalPeriodSelect.value !== 'custom');
        });
    }
    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', async () => {
            const period = modalPeriodSelect.value;
            const startDate = document.getElementById('revenueModalStartDate').value;
            const endDate = document.getElementById('revenueModalEndDate').value;

            applyFilterBtn.disabled = true;
            applyFilterBtn.textContent = 'Loading...';
            totalSaleValueEl.textContent = '...';

            try {
                const response = await fetch("{{ route('admin.filtered-revenue') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        period: period,
                        start_date: startDate,
                        end_date: endDate
                    })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const result = await response.json();

                const formattedRevenue = '₱' + new Intl.NumberFormat('en-PH', { maximumFractionDigits: 0 }).format(result.total_revenue);
                totalSaleValueEl.textContent = formattedRevenue;

            } catch (error) {
                console.error('Error fetching filtered revenue:', error);
                totalSaleValueEl.textContent = 'Error';
                alert('Could not fetch the filtered sales data.');
            } finally {
                applyFilterBtn.disabled = false;
                applyFilterBtn.textContent = 'Apply Filter';
                revenueModal.classList.add('hidden');
            }
        });
    }


    // --- UNIFIED AI ANALYSIS SCRIPT ---
    if (document.getElementById('ai-analysis-section')) {
        let countdownInterval;

        const anomaliesContentEl = document.getElementById('ai-anomalies-content');
        const recommendationsContentEl = document.getElementById('ai-recommendations-content');
        const chartAnalysisContentEl = document.getElementById('ai-chart-analysis-content');
        const aiModelSelect = document.getElementById('aiModelSelect');
        const refreshAiBtn = document.getElementById('refreshAiBtn');
        const speakAnalysisBtn = document.getElementById('speakAnalysisBtn');
        const timerContainerEl = document.getElementById('ai-timer-container');
        const timerEl = document.getElementById('ai-timer');
        const viewAiHistoryBtn = document.getElementById('viewAiHistoryBtn');
        const aiHistoryPanel = document.getElementById('ai-history-panel');
        const aiHistoryList = document.getElementById('ai-history-list');

        function saveSelectedAiModel(model) {
            localStorage.setItem('selectedAiModel', model);
        }

        function loadSelectedAiModel() {
            const savedModel = localStorage.getItem('selectedAiModel');
            if (savedModel && aiModelSelect.querySelector(`option[value="${savedModel}"]`)) {
                aiModelSelect.value = savedModel;
            }
        }

        function startCountdown(expiryDateString) {
            if (countdownInterval) clearInterval(countdownInterval);

            if (!expiryDateString) {
                timerContainerEl.style.display = 'none';
                if (refreshAiBtn) {
                    refreshAiBtn.disabled = false;
                    refreshAiBtn.title = 'Get a new AI analysis.';
                }
                return;
            }

            timerContainerEl.style.display = 'block';
            const expiryDate = new Date(expiryDateString);

            const updateTimerAndButton = () => {
                const now = new Date();
                const diff = expiryDate - now;

                if (diff <= 0) {
                    clearInterval(countdownInterval);
                    timerEl.textContent = 'Analysis expired. Refresh needed.';
                    if (refreshAiBtn) {
                        refreshAiBtn.disabled = false;
                        refreshAiBtn.title = 'Get a new AI analysis.';
                    }
                    return;
                }

                if (refreshAiBtn) {
                    refreshAiBtn.disabled = true;
                    refreshAiBtn.title = 'Please wait for the cooldown to finish before refreshing.';
                }

                const minutes = Math.floor((diff / 1000 / 60) % 60);
                const seconds = Math.floor((diff / 1000) % 60);
                timerEl.textContent = `New analysis in: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            };

            updateTimerAndButton();
            countdownInterval = setInterval(updateTimerAndButton, 1000);
        }

        function renderAnomalies(anomalies) {
            if (!anomalies || anomalies.length === 0) {
                anomaliesContentEl.innerHTML = `<div class="anomaly-item positive bg-gray-50 p-3 rounded-lg border-l-4 flex items-start gap-3"><i class="fas fa-check-circle text-green-500 mt-1"></i><p class="text-sm text-green-700">No critical anomalies detected.</p></div>`;
                return;
            }
            const anomaliesHtml = anomalies.map(anomaly => {
                let iconClass, iconColor;
                if (anomaly.type === 'positive') { iconClass = 'fa-check-circle'; iconColor = 'text-green-500'; }
                else if (anomaly.type === 'negative') { iconClass = 'fa-exclamation-triangle'; iconColor = 'text-red-500'; }
                else { iconClass = 'fa-info-circle'; iconColor = 'text-yellow-500'; }
                return `<div class="anomaly-item ${anomaly.type} bg-gray-50 p-3 rounded-lg border-l-4 flex items-start gap-3"><i class="fas ${iconClass} ${iconColor} mt-1"></i><p class="text-sm text-gray-700">${anomaly.message || 'No message.'}</p></div>`;
            }).join('');
            anomaliesContentEl.innerHTML = anomaliesHtml;
        }

        function renderRecommendations(recommendations) {
            if (!recommendations || recommendations.length === 0) {
                recommendationsContentEl.innerHTML = `<div class="bg-gray-50 p-4 rounded-lg"><p class="text-sm text-gray-600">No specific recommendations at this time.</p></div>`;
                return;
            }
            const recommendationsHtml = recommendations.map(rec => `<div class="recommendation-card text-white p-4 rounded-lg shadow-lg flex items-start gap-3"><i class="fas fa-lightbulb mt-1"></i><p class="font-medium flex-1">${rec.message || 'No message.'}</p></div>`).join('');
            recommendationsContentEl.innerHTML = recommendationsHtml;
        }

        function renderChartAnalysis(analysis) {
            if (!analysis) {
                chartAnalysisContentEl.innerHTML = `<p class="text-gray-500">Could not generate chart analysis.</p>`;
                speakAnalysisBtn.classList.add('hidden');
                return;
            }
            chartAnalysisContentEl.innerHTML = `<p>${analysis}</p>`;
            speakAnalysisBtn.classList.remove('hidden');
        }

        function renderFullAnalysis(analysisData) {
            if(analysisData) {
                renderAnomalies(analysisData.anomalies);
                renderRecommendations(analysisData.recommendations);
                renderChartAnalysis(analysisData.chart_analysis);
            }
        }

        function renderAnalysisHistory(historyData) {
            aiHistoryList.innerHTML = '';
            if (!historyData || historyData.length === 0) {
                aiHistoryList.innerHTML = '<p class="text-sm text-gray-500">No past analyses found.</p>';
                return;
            }

            historyData.forEach((item, index) => {
                const date = new Date(item.created_at);
                const formattedDate = date.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });

                const historyItemEl = document.createElement('div');
                historyItemEl.className = 'history-item p-2 rounded-md border-l-4 border-transparent';
                historyItemEl.innerHTML = `
                    <p class="font-medium text-sm text-gray-800">Analysis from: ${formattedDate}</p>
                    <p class="text-xs text-gray-600">Model: ${item.summary_data._model_used || 'Unknown'}</p>
                `;
                historyItemEl.dataset.analysis = JSON.stringify(item.summary_data);

                if (index === 0) {
                    historyItemEl.classList.add('active');
                }

                historyItemEl.addEventListener('click', (e) => {
                    document.querySelectorAll('.history-item').forEach(el => el.classList.remove('active'));
                    e.currentTarget.classList.add('active');
                    const analysisData = JSON.parse(e.currentTarget.dataset.analysis);
                    renderFullAnalysis(analysisData);
                });

                aiHistoryList.appendChild(historyItemEl);
            });
        }

        function showLoadingState() {
            const loaderHtml = `<div class="loader"><svg class="animate-spin -ml-1 mr-3 h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="text-lg font-semibold">Loading AI analysis...</span></div>`;
            anomaliesContentEl.innerHTML = loaderHtml;
            recommendationsContentEl.innerHTML = '';
            chartAnalysisContentEl.innerHTML = '';
            speakAnalysisBtn.classList.add('hidden');
            startCountdown(null);
        }

        function handleError(error) {
            console.error("Error fetching AI analysis:", error);
            const errorMessage = error.message || 'Could not fetch analysis.';
            renderAnomalies([{type: 'negative', message: `Analysis failed: ${errorMessage}`}]);
            renderRecommendations([]);
            chartAnalysisContentEl.innerHTML = `<p class="text-red-600">Chart analysis failed.</p>`;
            startCountdown(new Date(Date.now() + 2 * 60 * 1000).toISOString());
        }

        async function getCombinedAnalysis(force = false) {
            if (window.isFetchingAi) return;
            window.isFetchingAi = true;

            if (refreshAiBtn) {
                refreshAiBtn.disabled = true;
                refreshAiBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Refreshing...`;
            }

            showLoadingState();

            // MODIFIED: Parse the key type and model name from the single dropdown's composite value
            const compositeValue = aiModelSelect.value;
            const separatorIndex = compositeValue.indexOf(':');
            const selectedApiKeyType = compositeValue.substring(0, separatorIndex);
            const selectedAiModel = compositeValue.substring(separatorIndex + 1);

            const chartsToAnalyze = {};
            document.querySelectorAll('.chart-container').forEach(container => {
                if (container.style.display !== 'none') {
                    const chartId = container.dataset.chartId;
                    const chartInstance = chartInstances[`${chartId}Chart`];
                    if (chartInstance) {
                        chartsToAnalyze[chartId] = {
                            name: container.querySelector('h3')?.textContent || 'Unknown Chart',
                            labels: chartInstance.data.labels,
                            values: chartInstance.data.datasets.map(d => d.data),
                            datasetLabels: chartInstance.data.datasets.map(d => d.label)
                        };
                    }
                }
            });

            try {
                const response = await fetch("{{ route('admin.ai.handler') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify({
                        request_type: 'dashboard_analysis',
                        ai_model: selectedAiModel,
                        api_key_type: selectedApiKeyType,
                        chart_data: chartsToAnalyze,
                        force_refresh: force
                    })
                });
                if (!response.ok) { throw new Error( (await response.json()).error || 'Failed to fetch from server.'); }

                const result = await response.json();

                renderFullAnalysis(result.analysis);
                startCountdown(result.expires_at);

                if (result.history) {
                    renderAnalysisHistory(result.history);
                }

                saveSelectedAiModel(compositeValue);

            } catch (error) {
                handleError(error);
            } finally {
                window.isFetchingAi = false;
                if (refreshAiBtn) {
                    refreshAiBtn.innerHTML = `<i class="fas fa-redo-alt"></i> Refresh`;
                }
            }
        }

        loadSelectedAiModel();

        refreshAiBtn.addEventListener('click', () => getCombinedAnalysis(true));
        
        aiModelSelect.addEventListener('change', () => {
            saveSelectedAiModel(aiModelSelect.value);
        });

        speakAnalysisBtn.addEventListener('click', () => {
            const analysisText = chartAnalysisContentEl.querySelector('p')?.textContent;
            if (analysisText) {
                const utterance = new SpeechSynthesisUtterance(analysisText);
                utterance.lang = 'en-US';
                window.speechSynthesis.speak(utterance);
            }
        });

        viewAiHistoryBtn.addEventListener('click', () => {
            aiHistoryPanel.classList.toggle('hidden');
        });

        getCombinedAnalysis(false);
    }

    // --- CHARTING LOGIC ---
    const chartsContainer = document.getElementById('chartsContainer');
    const leftCharts = document.getElementById('leftCharts');
    const rightCharts = document.getElementById('rightCharts');
    const chartFilterSelect = document.getElementById('chartFilter');
    const customChartSelectionDiv = document.getElementById('customChartSelection');
    const applyCustomChartsBtn = document.getElementById('applyCustomCharts');
    const allChartContainers = document.querySelectorAll('#chartsContainer .chart-container');

    function resizeAllCharts() {
        Object.values(chartInstances).forEach(chart => {
            if (chart && typeof chart.resize === 'function') {
                chart.resize();
            }
        });
    }

    function applyChartFilter() {
        const filterValue = chartFilterSelect.value;
        customChartSelectionDiv.classList.toggle('hidden', filterValue !== 'custom');
        if (filterValue === 'custom') return;

        allChartContainers.forEach(container => {
            const chartId = container.dataset.chartId;
            const shouldShow = (filterValue === 'all') || (filterValue === chartId);
            container.style.display = shouldShow ? 'block' : 'none';
        });
        setTimeout(resizeAllCharts, 10);
    }

    function applyCustomFilter() {
        const selectedCharts = Array.from(document.querySelectorAll('input[name="customChart"]:checked')).map(cb => cb.value);
        allChartContainers.forEach(container => {
            const chartId = container.dataset.chartId;
            container.style.display = selectedCharts.includes(chartId) ? 'block' : 'none';
        });
        setTimeout(resizeAllCharts, 10);
    }

    if (chartFilterSelect) chartFilterSelect.addEventListener('change', applyChartFilter);
    if (applyCustomChartsBtn) applyCustomChartsBtn.addEventListener('click', applyCustomFilter);

    function destroyChart(canvasId) {
        if (chartInstances[canvasId]) {
            chartInstances[canvasId].destroy();
            delete chartInstances[canvasId];
        }
    }

    function createChart(canvasId, options) {
        destroyChart(canvasId);
        const canvasContainer = document.getElementById(canvasId + 'Container');
        if (!canvasContainer) return null;
        canvasContainer.innerHTML = `<canvas id="${canvasId}"></canvas>`;
        const ctx = document.getElementById(canvasId)?.getContext('2d');
        if (ctx) {
            const newChart = new Chart(ctx, options);
            chartInstances[canvasId] = newChart;
            return newChart;
        }
        return null;
    }

    function showChartLoader(containerId, text) {
        const container = document.getElementById(containerId);
        if(container) {
            container.innerHTML = `<div class="loader h-full"><svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="text-lg font-semibold text-blue-600">${text}</span></div>`;
        }
    }

    async function updateRevenueChart() {
        const period = document.getElementById('revenueTimePeriod')?.value;
        const year = document.getElementById('revenueYearFilter')?.value;
        const month = (period !== 'year') ? document.getElementById('revenueMonthFilter')?.value : '';
        const week = (period === 'week') ? document.getElementById('revenueWeekFilter')?.value : '';
        showChartLoader('revenueChartContainer', 'Loading revenue data...');
        try {
            const response = await fetch(`/admin/revenue-data/${period}/${year}/${month || '0'}/${week || '0'}`);
            if (!response.ok) throw new Error('Network response error');
            const data = await response.json();
            createChart('revenueChart', {
                type: 'line',
                data: { labels: data.labels, datasets: [{ label: 'Revenue (Delivered Orders)', data: data.values, borderColor: 'rgba(59, 130, 246, 1)', backgroundColor: 'rgba(59, 130, 246, 0.1)', borderWidth: 2, tension: 0.3, fill: true }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { callback: (v) => '₱' + v.toLocaleString('en-PH') } } }, interaction: { intersect: false, mode: 'index' } }
            });
        } catch (error) {
            console.error('Error fetching revenue data:', error);
            document.getElementById('revenueChartContainer').innerHTML = '<p class="text-center text-red-500">Could not load revenue data.</p>';
        }
    }

    function updatePerformanceChart(type) {
         const performanceData = {
              mostSold: { labels: @json($labels), data: @json($data), backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', label: 'Most Ordered Products' },
              moderateSold: { labels: @json($moderateSoldLabels), data: @json($moderateSoldData), backgroundColor: 'rgba(75, 192, 192, 0.6)', borderColor: 'rgba(75, 192, 192, 1)', label: 'Moderately Ordered Products' },
              lowSold: { labels: @json($lowSoldLabels), data: @json($lowSoldData), backgroundColor: 'rgba(255, 99, 132, 0.6)', borderColor: 'rgba(255, 99, 132, 1)', label: 'Low Ordered Products' }
       };
        const chartData = performanceData[type];
        createChart('productPerformanceChart', {
            type: 'bar',
            data: { labels: chartData.labels, datasets: [{ label: chartData.label, data: chartData.data, backgroundColor: chartData.backgroundColor, borderColor: chartData.borderColor, borderWidth: 1 }] },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Quantity Sold' } }, x: { title: { display: true, text: 'Product' } } } }
        });
    }

    async function updateDeductedChart() {
        const year = document.getElementById('deductedYearFilter')?.value;
        const month = document.getElementById('deductedMonthFilter')?.value;
        const location = document.getElementById('deductedLocationFilter')?.value || '';
        showChartLoader('deductedQuantitiesChartContainer', 'Loading products delivered...');
        try {
            const response = await fetch(`/admin/filtered-deducted-quantities/${year}/${month}/${location}`);
            if (!response.ok) throw new Error('Network error');
            const data = await response.json();
            createChart('deductedQuantitiesChart', {
                type: 'bar',
                data: { labels: data.labels, datasets: [{ label: 'Quantity Delivered', data: data.deductedData, backgroundColor: 'rgba(54, 162, 235, 0.6)', borderWidth: 1 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true }, x: { title: { display: true, text: 'Product' } } } }
            });
        } catch (error) {
            console.error('Error fetching deducted quantities:', error);
            document.getElementById('deductedQuantitiesChartContainer').innerHTML = '<p class="text-center text-red-500">Could not load delivered products data.</p>';
        }
    }

    async function updateInventoryChart() {
        const locationId = document.getElementById('inventoryLocationFilter')?.value;
        const url = locationId ? `/admin/inventory-levels/${locationId}` : '/admin/inventory-levels';
        showChartLoader('inventoryLevelsChartContainer', 'Loading inventory data...');
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Network error fetching inventory data');
            const data = await response.json();
            createChart('inventoryLevelsChart', {
                type: 'bar',
                data: { labels: data.labels, datasets: [{ label: 'Current Stock', data: data.inventoryData, backgroundColor: 'rgba(255, 99, 132, 0.6)', borderWidth: 1 }] },
                options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', scales: { x: { beginAtZero: true, title: { display: true, text: 'Quantity' } } } }
            });
        } catch (error) {
            console.error('Error fetching inventory data:', error);
            document.getElementById('inventoryLevelsChartContainer').innerHTML = '<p class="text-center text-red-500">Could not load inventory data.</p>';
        }
    }

    async function fetchAndUpdateTrendData() {
        const season = document.getElementById('seasonFilter')?.value;
        const year = document.getElementById('trendYearFilter')?.value;
        const predictionContainer = document.getElementById('predictionCardsContainer');
        showChartLoader('seasonalTrendsChartContainer', 'Loading trend data...');
        predictionContainer.innerHTML = `<div class="loader col-span-3">Loading predictions...</div>`;
        try {
            const response = await fetch(`/admin/trending-products?season=${season}&year=${year}`);
            if (!response.ok) throw new Error('Network error');
            const data = await response.json();

            createChart('seasonalTrendsChart', {
                    type: 'bar',
                    data: {
                        labels: data.trending_products.map(p => p.generic_name),
                        datasets: [
                            { label: 'Current Month Sales', data: data.trending_products.map(p => p.current_sales), backgroundColor: 'rgba(54, 162, 235, 0.6)'},
                            { label: 'Next Month Predicted', data: data.trending_products.map(p => p.next_month_prediction), backgroundColor: 'rgba(255, 159, 64, 0.8)', borderColor: 'rgba(255, 159, 64, 1)', type: 'line', tension: 0.3, borderWidth: 2 },
                            { label: 'Historical Average', data: data.trending_products.map(p => p.historical_avg), backgroundColor: 'rgba(75, 192, 192, 0.6)', borderColor: 'rgba(75, 192, 192, 1)', type: 'line', tension: 0.3, borderWidth: 2, borderDash: [5, 5] }
                        ]
                    },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });

            predictionContainer.innerHTML = '';
            if(data.predicted_peaks.length === 0) {
                    predictionContainer.innerHTML = '<p class="text-gray-500 col-span-3 text-center">No prediction available for this filter.</p>';
            } else {
                    data.predicted_peaks.forEach(p => {
                        const card = document.createElement('div');
                        card.className = 'bg-white p-4 rounded-lg shadow-md border-l-4 animate__animated animate__fadeInUp';

                        const percentage = p.prediction_percentage_change;
                        const statusText = p.prediction_status_text;

                        let colorClass = 'text-gray-600';
                        let iconClass = 'fa-solid fa-minus';
                        let borderColor = 'border-gray-400';

                        if (percentage >= 25) {
                            colorClass = 'text-green-600';
                            iconClass = 'fa-solid fa-arrow-trend-up';
                            borderColor = 'border-green-500';
                        } else if (percentage > 5) {
                            colorClass = 'text-green-500';
                            iconClass = 'fa-solid fa-arrow-up';
                            borderColor = 'border-green-400';
                        } else if (percentage < -20) {
                            colorClass = 'text-red-600';
                            iconClass = 'fa-solid fa-arrow-trend-down';
                            borderColor = 'border-red-500';
                        } else if (percentage < -5) {
                            colorClass = 'text-yellow-600';
                            iconClass = 'fa-solid fa-arrow-down';
                            borderColor = 'border-yellow-500';
                        }

                        card.classList.add(borderColor);

                        card.innerHTML = `
                            <h4 class="font-semibold text-gray-800 truncate">${p.generic_name}</h4>
                            <p class="text-sm text-gray-600 truncate">${p.brand_name}</p>
                            <p class="text-xs text-gray-500 mb-2">${p.strength ? `${p.strength} - ` : ''}${p.form}</p>
                            <div class="text-2xl font-bold ${colorClass} mb-2 flex items-center">
                                <i class="${iconClass} mr-2 fa-fw"></i>
                                <span>${percentage > 0 ? '+' : ''}${percentage.toFixed(0)}%</span>
                            </div>
                            <p class="text-xs text-gray-500">${statusText}</p>
                        `;

                        predictionContainer.appendChild(card);
                    });
            }

        } catch(error) {
            console.error('Error fetching trend data:', error);
            document.getElementById('seasonalTrendsChartContainer').innerHTML = '<p class="text-center text-red-500">Could not load trends data.</p>';
            predictionContainer.innerHTML = '<p class="text-gray-500 col-span-3 text-center">An error occurred while analyzing predictions.</p>';
        }
    }

    function createOrderStatusChart() {
        createChart('orderStatusChart', {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'Pending', 'Cancelled'],
                datasets: [{
                    label: 'Order Count',
                    data: [{{ $orderStatusCounts['delivered'] ?? 0 }}, {{ $orderStatusCounts['pending'] ?? 0 }}, {{ $orderStatusCounts['cancelled'] ?? 0 }}],
                    backgroundColor: ['rgba(75, 192, 192, 0.8)', 'rgba(255, 205, 86, 0.8)', 'rgba(255, 99, 132, 0.8)']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // --- Layout and Initialization ---
    function toggleSortableBasedOnViewport() {
        const isDisabled = window.innerWidth < XL_BREAKPOINT;
        if (sortableInstances.left) {
            sortableInstances.left.option("disabled", isDisabled);
        }
        if (sortableInstances.right) {
            sortableInstances.right.option("disabled", isDisabled);
        }
    }

    function initializeSortable() {
        if (leftCharts && rightCharts) {
            sortableInstances.left = Sortable.create(leftCharts, { group: 'charts', animation: 150, onEnd: saveChartLayout });
            sortableInstances.right = Sortable.create(rightCharts, { group: 'charts', animation: 150, onEnd: saveChartLayout });
        }
        toggleSortableBasedOnViewport();
    }

    function saveChartLayout() {
        const leftIds = [...leftCharts.children].map(el => el.dataset.chartId);
        const rightIds = [...rightCharts.children].map(el => el.dataset.chartId);
        localStorage.setItem('dashboardChartLayout', JSON.stringify({ left: leftIds, right: rightIds }));
        Object.values(chartInstances).forEach(chart => chart.resize());
    }

    window.addEventListener('resize', toggleSortableBasedOnViewport);

    const initialChartRenders = async () => {
        await Promise.allSettled([
            updateRevenueChart(),
            updateDeductedChart(),
            updateInventoryChart(),
            fetchAndUpdateTrendData(),
        ]);
        updatePerformanceChart('mostSold');
        createOrderStatusChart();
        initializeSortable();
    };

    initialChartRenders();

    // Event Listeners for Chart Filters
    document.getElementById('revenueUpdateBtn')?.addEventListener('click', updateRevenueChart);
    document.getElementById('deductedYearFilter')?.addEventListener('change', updateDeductedChart);
    document.getElementById('deductedMonthFilter')?.addEventListener('change', updateDeductedChart);
    document.getElementById('deductedLocationFilter')?.addEventListener('change', updateDeductedChart);
    document.getElementById('inventoryLocationFilter')?.addEventListener('change', updateInventoryChart);
    document.getElementById('seasonFilter')?.addEventListener('change', fetchAndUpdateTrendData);
    document.getElementById('trendYearFilter')?.addEventListener('change', fetchAndUpdateTrendData);
    document.getElementById('mostSoldBtn')?.addEventListener('click', () => updatePerformanceChart('mostSold'));
    document.getElementById('moderateSoldBtn')?.addEventListener('click', () => updatePerformanceChart('moderateSold'));
    document.getElementById('lowSoldBtn')?.addEventListener('click', () => updatePerformanceChart('lowSold'));
});
</script>

@if(auth()->guard('staff')->check())
<script>
    if (navigator.geolocation) {
        setInterval(() => {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    fetch("{{ route('api.update-location') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        body: JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                        }),
                    });
                },
                function (error) {
                    console.error("Error getting location: ", error);
                }
            );
        }, 10000); // Send location every 10 seconds
    } else {
        console.error("Geolocation is not supported.");
    }
</script>
@endif
</body>
</html>