@if($blockedIps->isEmpty())
    <p class="text-center text-gray-500 p-4">No IP addresses found.</p>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Blocked At</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($blockedIps as $ip)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $ip->ip_address }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $ip->reason }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $ip->created_at->format('Y-m-d H:i:s') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <form action="{{ route('blocked-ips.destroy', $ip->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to unblock this IP?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                Unblock
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $blockedIps->links() }}
    </div>
@endif