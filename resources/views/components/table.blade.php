@props(['headings'=> [], 'variable' => null, 'secondaryVariable' => null,  'category' =>'none' ])
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
                    {{-- @php
                        dd($variable->toArray());
                    @endphp --}}
                    <tr class="text-center">
                        <td>{{ $inv->batch_number }}</td>
                        <td>{{ $inv->product->generic_name }}</td>
                        <td>{{ $inv->product->brand_name }}</td>
                        <td>{{ $inv->product->form }}</td>
                        <td>{{ $inv->product->strength }}</td>
                        <td>{{ $inv->quantity }}</td>
                        <td>{{ Carbon::parse($inv->expiry_date)->translatedFormat('M d, Y') }}</td>
                        <td>
                          <button 
    class="bg-blue-500 text-white px-4 py-2 rounded-md cursor-pointer"
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

            {{-- productdeals --}}
            @case($category === 'productdeals')
                @foreach ($variable as $company)
                    <tr class="text-center">
                        <td>{{ $company->id }}</td>
                        <td> {{ $company->name }} </td>
                        <td> 
                            {{ array_key_exists($company->name, $secondaryVariable->toArray()) ? count($secondaryVariable[$company->name])  : "No " }} Personalized Products 
                        </td>

                        {{-- button for view and add --}}
                        <td class="m-auto flex gap-4 justify-center font-semibold">
                            @if ($secondaryVariable->get($company->name))
                                <x-vieworder 
                                onclick="viewproductlisting('{{ $company->name }}')" 
                                name="View"
                                />                                
                            @endif
                            <button class="cursor-pointer py-1 rounded-lg" onclick="addproductlisting('{{ $company->id }}')"><i class="fa-regular fa-plus mr-1"></i>Add</button>
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

            {{-- manageaccount
            @case($category === 'manageaccount')
            @foreach($variable as $account)
            <tr 
                data-id="{{ $account['id'] }}" 
                data-name="{{ $account['name'] }}" 
                data-username="{{ $account['username'] ?? '' }}"
                data-email="{{ $account['email'] }}"
                data-role="{{ $account['role'] }}"
                data-location="{{ $account['location_id'] ?? '' }}"
                data-jobtitle="{{ $account['job_title'] ?? '' }}"
                data-adminid="{{ $account['admin_id'] ?? '' }}"
                data-contactnumber="{{ $account['contact_number'] ?? 'N/A' }}" >
                
                <td>{{ $account['id'] }}</td>
                <td>{{ $account['name'] ?? $account['username'] ?? $account['staff_username'] ?? 'N/A' }}</td>
                <td>{{ $account['email'] }}</td>
                <td>{{ ucfirst($account['role']) }}</td>
                <td>
                    {{ $account['company'] ?? 'RCT Med Pharma' }}
                </td>
                <td class="flex justify-center items-center gap-4">
                    <button class="text-[#005382] cursor-pointer" onclick="openEditAccountModal(this)">
                        <i class="fa-regular fa-pen-to-square mr-2"></i> Edit
                    </button>
                    <form id="deleteaccountform-{{ $account['id'] }}" method="POST" action="{{ route('superadmin.account.delete', ['role' => $account['role'], 'id' => $account['id']]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="deleteaccountbtn text-red-500 cursor-pointer" data-account-id="{{ $account['id'] }}">
                            <i class="fa-solid fa-trash mr-2"></i> Delete
                        </button>
                    </form>
          
        
                        </td>
                    </tr>
                @endforeach --}}
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
                                $order_calc = $order->exclusive_deal->price * $order->quantity;
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