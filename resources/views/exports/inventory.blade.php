@php
    use Carbon\Carbon;
@endphp

@switch(Str::lower($subType))
    @case('all')
        <h1>
            All Products Assigned in All Delivery Locations
        </h1>
        @break
    @case('tarlac')
        <h1>
            All Products Assigned in Delivery Location: Tarlac
        </h1>
        @break
    @case('nueva ecija')
        <h1>
            All Products Assigned in Delivery Location: Nueva Ecija
        </h1>
        @break

    @case('in-summary')
        <h1>
            All Products Currently In Stock in All Delivery Locations
        </h1>
        @break
    @case('low-summary')
        <h1>
            All Products Currently Low On Stock in All Delivery Locations
        </h1>
        @break
    @case('out-summary')
        <h1>
            All Products Out of Stock in All Delivery Locations
        </h1>
        @break
        
    @case('near-expiry-summary')
        <h1>
            All Products Near Expiration in All Delivery Locations
        </h1>
        @break
    @case('expired-summary')
        {{-- @php
                dd(Str::lower($subType));
            
        @endphp --}}


        <h1>
            All Products Currently Expired in All Delivery Locations
        </h1>
        @break
    @default
        
@endswitch


@if ($type == "grouped")
    @foreach ($inventory as $provinceName => $generalInfo)
        <h1> In: {{$provinceName}} </h1>
        <table>
            <thead>
                <tr>
                    <th colspan="2">Generic Name</th>
                    <th colspan="2">Brand Name</th>
                    <th colspan="2">Form</th>
                    <th colspan="2">Strength</th>
                    <th colspan="2">Quantity</th>
                    <th colspan="2">Date Created</th>
                </tr>
            </thead>    
                
            <tbody>
                @foreach ($generalInfo as $stocks)
                    <tr>
                        @foreach ($stocks['inventory'] as $stock)
                            <td colspan="2"> {{ $stock['product']['generic_name'] }} </td>
                            <td colspan="2"> {{ $stock['product']['brand_name'] }} </td>
                            <td colspan="2"> {{ $stock['product']['form'] }} </td>
                            <td colspan="2"> {{ $stock['product']['strength'] }} </td>
                            <td colspan="2"> {{ $stock['quantity'] }} </td>
                            <td colspan="2"> 
                                {{ Carbon::parse($stock['created_at'])->translatedFormat('j F Y h:i A') }} 
                            </td>
                            @break
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
@endif

@if ($type == "individual")
    @if (in_array($subType, ['near-expiry-summary', 'expired-summary']))
        @foreach ($inventory as $provinceName => $stocks)
            <h1>In: {{ $provinceName }}</h1>
            <table>
                <thead>
                    <tr>
                        <th colspan="2">Generic Name</th>
                        <th colspan="2">Brand Name</th>
                        <th colspan="2">Form</th>
                        <th colspan="2">Strength</th>
                        <th colspan="2">Quantity</th>
                        <th colspan="2">Expiry Date</th>
                        <th colspan="2">Date Created</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($stocks as $stock)
                        <tr class="text-center">
                            <td colspan="2">{{ $stock->product->generic_name }}</td>
                            <td colspan="2">{{ $stock->product->brand_name }}</td>
                            <td colspan="2">{{ $stock->product->form }}</td>
                            <td colspan="2">{{ $stock->product->strength }}</td>
                            <td colspan="2">{{ $stock->quantity }}</td>
                            <td colspan="2">{{ $stock->expiry_date }}</td>
                            <td colspan="2">{{ $stock->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    
    @else
        <table>
            <thead>
                <tr>
                    <th colspan="2">Generic Name</th>
                    <th colspan="2">Brand Name</th>
                    <th colspan="2">Form</th>
                    <th colspan="2">Strength</th>
                    <th colspan="2">Quantity</th>
                    <th colspan="2">Expiry Date</th>
                    <th colspan="2">Date Created</th>
                </tr>
            </thead>
            
            <tbody>
                @foreach ($inventory as $stock)
                    <tr class="text-center">
                        <td colspan="2">{{ $stock->product->generic_name }}</td>
                        <td colspan="2">{{ $stock->product->brand_name }}</td>
                        <td colspan="2">{{ $stock->product->form }}</td>
                        <td colspan="2">{{ $stock->product->strength }}</td>
                        <td colspan="2">{{ $stock->quantity }}</td>
                        <td colspan="2">{{ $stock->expiry_date }}</td>
                        <td colspan="2">{{ $stock->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endif
