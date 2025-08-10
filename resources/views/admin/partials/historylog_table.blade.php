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
                        <td class="flex justify-center">
                            <p class="py-1 px-3 text-white rounded-md w-20 text-center text-[12px]
                                {{ $log->event == 'Add' ? 'bg-blue-500/70' : 
                                ($log->event == 'Edit' ? 'bg-green-500/70' : 
                                ($log->event == 'Archive' ? 'bg-red-600/70' : 
                                ($log->event == 'Disapprove' ? 'bg-red-600/70' :
                                ($log->event == 'Approve' ? 'bg-green-600/70' : 'bg-gray-500/70 text-black')))) }}">
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