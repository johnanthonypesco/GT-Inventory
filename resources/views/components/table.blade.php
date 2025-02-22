@props(['headings'=> [], 'variable' => null, 'category' =>'none' ])

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
                        <td class="{{ $inv->quantity < 100 ? "text-yellow-600 font-semibold" : "text-green-500"}}">{{ $inv->quantity < 100 ? "Low Stock" : "In Stock"}}</td>
                    </tr>
                @endforeach
                @break

            {{-- productdeals --}}
            @case($category === 'productdeals')
                <tr>
                    <td>1234</td>
                    <td>Jewel Velasquez</td>
                    <td>20 Personalized Products</td>
                    {{-- button for view and add --}}
                    <td class="m-auto flex gap-4 justify-center font-semibold">
                        <x-vieworder onclick="viewproductlisting()" name="View"/>
                        <button class="cursor-pointer py-1 rounded-lg" onclick="addproductlisting()"><i class="fa-regular fa-plus mr-1"></i>Add</button>
                    </td>
                    {{-- button for view and add --}}
                </tr>
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
                <tr>
                    <td>1234</td>
                    <td>Jewel Velasquez</td>
                    <td>jewelvelasquez</td>
                    <td>******</td>
                    {{-- Action --}}
                    <td class="flex justify-center items-center gap-4">
                        <x-editbutton onclick="editaccount()"/>
                        <x-deletebutton route="admin.manageaccount" method="DELETE"/>
                    </td>
                    {{-- Action --}}
                </tr>           
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