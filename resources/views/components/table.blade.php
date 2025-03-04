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
                    <tr class="text-center">
                        <td>{{ $inv->batch_number }}</td>
                        <td>{{ $inv->product->generic_name }}</td>
                        <td>{{ $inv->product->brand_name }}</td>
                        <td>{{ $inv->product->form }}</td>
                        <td>{{ $inv->product->strength }}</td>
                        <td>{{ $inv->quantity }}</td>
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
            {{-- <script>
                alert("{{ $errors->first("status") }}")
            </script> --}}
                @foreach ($variable as $order)
                    <tr class="text-center">
                        <td> {{ $order->first()->id }} </td>
                        <td> {{ $order->first()->user->company->name }} </td>
                        <td> {{ $order->first()->user->name }} </td>
                        <td> 
                            {{ Carbon::parse($order->first()
                            ->date_ordered)->translatedFormat('M d, Y') 
                            }} 
                        </td>
                        <td>
                            <form action="{{ route('admin.order.update', $order->first()->id) }}" method="post">
                                @csrf
                                @method("PUT")
                                
                                <select onchange="this.form.submit()" name="status" id="status" 
                                class="py-1 px-2 rounded-lg border-3 outline-none text-center
                                {{
                                    match ($order->first()->status) {
                                        'pending' => 'border-orange-400',
                                        'completed' => 'border-blue-500',
                                        'partial-delivery' => 'border-purple-600',
                                        default => 'border-black'
                                    }
                                }}
                                ">
                                    <option @selected($order->first()->status == 'pending') value="pending">Pending</option>
                                    <option @selected($order->first()->status == 'completed') value="completed">Completed</option>
                                    <option @selected($order->first()->status == 'cancelled') value="cancelled">Cancelled</option>
                                    <option @selected($order->first()->status == 'partial-delivery') value="partial-delivery">Partial-Delivery</option>
                                    <option @selected($order->first()->status == 'delivered') value="delivered">Delivered</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <x-vieworder onclick="viewOrder('{{ $order->first()->id }}')" name="View Order"/>
                        </td>
                    </tr>
                @endforeach
            @break

            {{-- manageaccount --}}
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
                            <form id="deleteaccountform" method="POST" action="{{ route('superadmin.account.delete', ['role' => $account['role'], 'id' => $account['id']]) }}">
                                @csrf
                                @method('DELETE')
                                <button id="deleteaccountbtn" type="button" class="text-red-500 cursor-pointer">
                                    <i class="fa-solid fa-trash mr-2"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @break

            {{-- history --}}
            @case($category === 'history')
                <tr class="text-center">
                    <td>#123456</td>
                    <td>Jewel Velasquez</td>
                    <td>₱ 10,000</td>
                    <td>12/15/2023</td>
                    <td><p class="bg-[#172A95]/76 text-white py-1 px-2 rounded-lg w-fit m-auto uppercase">Delivered</p></td>
                    <td>
                        <x-vieworder onclick="viewOrder()" name="View Order"/>
                    </td>
                </tr> 
            @break

            @default
            <tr></tr>
        @endswitch
    </tbody>
</table>

<script src="{{asset('js/sweetalert/deleteaccountsweetalert.js')}}"></script>