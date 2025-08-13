@props(['headings'=> [], 'variable' => null, 'secondaryVariable' => null,  'category' =>'none', 'dealSearchCompany' => null ])
@php
    use Carbon\Carbon;
@endphp

<table class="w-full min-w-[600px]">
    <thead>
        <tr>
            @foreach ($headings as $heading)
                <th class="p-2 font-regular">{{ $heading }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @switch($category)
            {{-- inventory --}}
            @case($category === 'inventory')
                @foreach ($variable as $inv)
                    <tr class="text-center">
                        <td>{{ $inv->batch_number }}</td>
                        <td>{{ $inv->product->generic_name }}</td>
                        <td>{{ $inv->product->brand_name }}</td>
                        <td>{{ $inv->product->form }}</td>
                        <td>{{ $inv->product->strength }}</td>
                        <td class="{{  $inv->quantity > 0 ? '' : 'text-red-600 font-bold'}}">{{ $inv->quantity > 0 ? $inv->quantity : 'Empty'  }}</td>
                        <td>{{ Carbon::parse($inv->expiry_date)->translatedFormat('M d, Y') }}</td>
                        <td class="flex justify-center gap-2">
                            {{-- <button class="cursor-pointer bg-blue-600 text-white px-3 rounded-xl flex justify-center items-center">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button> --}}

                            <div class="relative group inline-block">
                                <button class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-xl flex justify-center items-center" onclick="openStockEditModal({ 
                                    id: '{{$inv->inventory_id}}',
                                    batch_number: '{{$inv->batch_number}}',
                                    generic_name: '{{$inv->product->generic_name}}',
                                    brand_name: '{{$inv->product->brand_name}}',
                                    quantity: '{{$inv->quantity}}',
                                    expiry_date: '{{$inv->expiry_date}}',
                                })">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                                
                                <!-- Tooltip -->
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block group-hover:animate-bounce
                                            bg-gray-800 text-white text-lg px-2 py-1 rounded-md whitespace-nowrap z-10">
                                    Edit the Stock Info
                                </div>
                            </div>

                          <button 
                            class="bg-green-600 text-white px-4 py-2 rounded-md cursor-pointer"
                            onclick="openTransferModal(
                                '{{ $inv->inventory_id }}', 
                                '{{ $inv->batch_number }}', 
                                '{{ $inv->product->name }}', 
                                '{{ $inv->location->province }}'
                            )"
                        >
                            Transfer
                        </button>
                        </td>
                    </tr>
                @endforeach
            @break

            @case($category === 'archive-inventory')
                @foreach ($variable as $inv)
                    <tr class="text-center">
                        <td>{{ $inv->batch_number }}</td>
                        <td>{{ $inv->product->generic_name }}</td>
                        <td>{{ $inv->product->brand_name }}</td>
                        <td>{{ $inv->product->form }}</td>
                        <td>{{ $inv->product->strength }}</td>
                        <td class="{{  $inv->quantity > 0 ? '' : 'text-red-600 font-bold'}}">{{ $inv->quantity > 0 ? $inv->quantity : 'Empty'  }}</td>
                        <td>{{ Carbon::parse($inv->expiry_date)->translatedFormat('M d, Y') }}</td>
                    </tr>
                @endforeach
            @break

            {{-- productdeals --}}
            @case($category === 'productdeals')
                @foreach ($variable as $company)
                    <tr class="text-center">
                        <td>{{ $company->id }}</td>
                        <td> {{ $company->name }} </td>
                        <td id="real-timer-total-personal-counter" data-company="{{ $company->name }}"> 
                            {{ isset($secondaryVariable[$company->name]) ? 
                            $secondaryVariable[$company->name]->total() 
                            : 'No' }} {{ $dealSearchCompany === $company->name ? "Searched" : "" }} Personalized Products
                        </td>

                        {{-- button for view and add --}}
                        <td class="m-auto flex gap-4 justify-center font-semibold">
                            @if ($secondaryVariable->get($company->name))
                                <x-vieworder 
                                onclick="viewproductlisting('{{ $company->name }}')" 
                                name="View"
                                />                                
                            @endif
                            <button class="cursor-pointer py-1 rounded-lg text-md" onclick="addproductlisting('{{ $company->id }}')"><i class="fa-regular fa-plus mr-1"></i>Add</button>
                        </td>
                    </tr>
                @endforeach                
            @break

            {{-- order --}}
            @case($category === 'order')
                @foreach ($variable as $employeeNameAndDate => $statuses)   
                    @php
                        $separatedNameAndDate = explode('|', $employeeNameAndDate);    
                    @endphp
                    
                    <tr class="text-center">
                        <td> {{ $separatedNameAndDate[0] }} </td>
                        <td> 
                            {{ Carbon::parse($separatedNameAndDate[1])->translatedFormat('M d, Y') }}
                        </td>
                        
                        <td>
                            <x-vieworder onclick="viewOrder('{{ $employeeNameAndDate }}')" name="View Order"/>
                        </td>
                    </tr>
                @endforeach
            @break

            {{-- history --}}
            @case($category === 'history')
                @foreach ($variable as $employeeName => $statuses)
                    {{-- FOR THE TOTAL PRICE COLUMN --}}
                    @php
                        $totalPrice = 0;
                    @endphp

                    @foreach ($statuses as $orders)
                        @foreach ($orders as $order)
                            @php
                                $order_calc = $order->price * $order->quantity;
                                $totalPrice += $order_calc;
                            @endphp
                        @endforeach
                    @endforeach
                    {{-- FOR THE TOTAL PRICE COLUMNS --}}

                    {{-- FOR THE NAME AND DATE COLUMNS --}}
                    @php
                        $separated = explode('|', $employeeName);
                    @endphp
                    {{-- FOR THE NAME AND DATE COLUMNS --}}

                    <tr>
                        <td>{{ $separated[0] }}</td>
                        <td>{{ Carbon::parse($separated[1])->translatedFormat('M d, Y') }}</td>
                        <td>₱ {{ number_format($totalPrice) }}</td>
                        <td>
                            <x-vieworder onclick="viewOrder('{{ $employeeName }}')" name="View Order"/>
                        </td>
                    </tr> 
                @endforeach
            @break

            @default
            <tr></tr>
        @endswitch
    </tbody>
</table>

<script src="{{asset('js/sweetalert/deleteaccountsweetalert.js')}}"></script>