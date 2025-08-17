<div class="overflow-x-auto mt-5 h-[60vh]">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Event</th>
                <th>Description</th>
                <th>Action By</th>
            </tr>
        </thead>
       <tbody>
    @if($historylogs->isEmpty())
        <tr>
            <td colspan="4" class="text-center p-4">No matching history logs found.</td>
        </tr>
    @else
        @foreach($historylogs as $log)
            <tr>
                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('F d, Y') }} <span class="font-light ml-2">{{ \Carbon\Carbon::parse($log->created_at)->format('h:i A') }}</span></td>
                @php
                // This array now defines the text color and the stripe (before:) color
                $stripeColors = [
                    'Add'          => 'text-blue-700 before:bg-blue-500',
                    'Edit'         => 'text-yellow-700 before:bg-yellow-500',
                    'Archive'      => 'text-orange-700 before:bg-orange-500',
                    'Disapprove'   => 'text-red-700 before:bg-red-500',
                    'Approve'      => 'text-sky-700 before:bg-sky-500',
                    'Delete'       => 'text-red-700 before:bg-red-500',
                    'Restore'      => 'text-lime-700 before:bg-lime-600',
                    'Update'       => 'text-green-700 before:bg-green-500',
                    'Failed Login' => 'text-red-700 before:bg-red-500',
                    'Login'        => 'text-green-700 before:bg-green-500',
                    'Logout'       => 'text-slate-600 before:bg-slate-500',
                ];
                $eventClasses = $stripeColors[$log->event] ?? 'text-gray-700 before:bg-gray-500';
                @endphp

                {{-- ✅ NEW HTML & CSS FOR THE EVENT COLUMN --}}
                <td>
                    <p class="relative py-1 pl-4 pr-3 bg-gray-100 rounded text-sm font-medium
                               before:content-[''] before:absolute before:left-0 before:top-0
                               before:bottom-0 before:w-1 {{ $eventClasses }}">
                        {{ $log->event }}
                    </p>
                </td>

                <td>{{ $log->description }}</td>
                <td>{{ $log->user_email ?? 'Unknown User' }}</td>
            </tr>
        @endforeach
    @endif
</tbody>
    </table>
</div>
{{-- Pagination Links --}}
<div class="pagination mt-6">
    {{ $historylogs->links() }}
</div>

{{-- to fix the bugs filter and paginate dont use this --}}


{{-- <div class="pagination">
    <x-pagination 
        currentPage="{{ $historylogs->currentPage() }}" 
        totalPage="{{ $historylogs->lastPage() }}" 
        prev="{{ $historylogs->previousPageUrl() }}" 
        next="{{ $historylogs->nextPageUrl() }}" 
    />
</div> --}}