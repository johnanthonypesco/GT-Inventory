@props(['headings'=> [], 'variable' => null, 'secondaryVariable' => null,  'category' =>'none' ])

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
                        <td>{{ $inv->product->brand_name }}</td>
                        <td>{{ $inv->product->generic_name }}</td>
                        <td>{{ $inv->product->form }}</td>
                        <td>{{ $inv->product->strength }}</td>
                        <td>{{ $inv->quantity }}</td>
                        <td>{{ $inv->expiry_date }}</td>
                    </tr>
                @endforeach
                @break

            {{-- productdeals --}}
            @case($category === 'productdeals')
                @foreach ($variable as $customer)
                    <tr class="text-center">
                        <td>{{ $customer->id }}</td>
                        <td> {{ $customer->name }} </td>
                        <td> 
                            {{ array_key_exists($customer->name, $secondaryVariable->toArray()) ? count($secondaryVariable[$customer->name])  : "No " }} Personalized Products 
                        </td>

                        {{-- button for view and add --}}
                        <td class="m-auto flex gap-4 justify-center font-semibold">
                            @if ($secondaryVariable->get($customer->name))
                                <x-vieworder 
                                onclick="viewproductlisting('{{ $customer->name }}')" 
                                name="View"
                                />                                
                            @endif
                            <button class="cursor-pointer py-1 rounded-lg" onclick="addproductlisting('{{ $customer->id }}')"><i class="fa-regular fa-plus mr-1"></i>Add</button>
                        </td>
                    </tr>
                @endforeach                
                @break

            {{-- order --}}
            @case($category === 'order')
                <tr class="text-center">
                    <td>#123456</td>
                    <td>Jewel Velasquez</td>
                    <td>₱ 10,000</td>
                    <td>
                        <select name="status" id="status" class="py-1 px-2 rounded-lg border border-[#005382] outline-none">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="delivered">Delivered</option>
                        </select>
                    </td>
                    <td>
                        <x-vieworder onclick="viewOrder()" name="View Order"/>
                    </td>
                </tr>
                <tr class="text-center">
                    <td>#123456</td>
                    <td>Jewel Velasquez</td>
                    <td>₱ 10,000</td>
                    <td>
                        <select name="status" id="status" class="py-1 px-2 rounded-lg border border-[#005382] outline-none">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="delivered">Delivered</option>
                        </select>
                    </td>
                    <td>
                        <x-vieworder onclick="viewOrder()" name="View Order"/>
                    </td>
                </tr>
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