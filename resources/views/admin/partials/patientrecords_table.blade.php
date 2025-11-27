<div class="overflow-x-auto p-5">
    <table class="w-full text-sm text-left">
        <thead class="sticky top-0 bg-gray-200 dark:bg-gray-700">
            <tr>
                <th class="p-3 text-gray-700 dark:text-gray-300 uppercase font-bold">#</th>
                @if(in_array(auth()->user()->user_level_id, [1, 2]))
                    <th class="p-3 text-gray-700 dark:text-gray-300 uppercase font-bold">Branch</th>
                @endif
                <th class="p-3 text-gray-700 dark:text-gray-300 uppercase font-bold">Resident Details</th>
                <th class="p-3 text-gray-700 dark:text-gray-300 uppercase font-bold text-center">Category</th>
                <th class="p-3 text-gray-700 dark:text-gray-300 uppercase font-bold">Date Dispensed</th>
                <th class="p-3 text-gray-700 dark:text-gray-300 uppercase font-bold text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($patientrecords as $record)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                    data-record-id="{{ $record->id }}"
                    data-patient-name="{{ $record->patient_name }}"
                    data-barangay-id="{{ $record->barangay_id }}"
                    data-purok="{{ $record->purok }}"
                    data-category="{{ $record->category }}"
                    data-date-dispensed="{{ $record->date_dispensed->format('Y-m-d') }}"
                    {{-- Pre-load medications as JSON for the View Modal --}}
                    data-medications="{{ json_encode($record->dispensedMedications->map(function($m){
                        return [
                            'batch' => $m->batch_number,
                            'medication' => $m->generic_name,
                            'brand' => $m->brand_name,
                            'form' => $m->form,
                            'strength' => $m->strength,
                            'quantity' => $m->quantity
                        ];
                    })) }}"
                >
                    <td class="p-3">{{ $loop->iteration + ($patientrecords->currentPage() - 1) * $patientrecords->perPage() }}</td>
                    
                    @if(in_array(auth()->user()->user_level_id, [1, 2]))
                        <td class="p-3"><span class="px-2 py-1 bg-gray-100 rounded text-xs font-bold">{{ $record->branch->name ?? 'N/A' }}</span></td>
                    @endif

                    <td class="p-3">
                        <p class="font-bold text-gray-800 dark:text-gray-200 capitalize">{{ $record->patient_name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ $record->barangay->barangay_name ?? '' }}, {{ $record->purok }}</p>
                    </td>
                    
                    <td class="p-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                            {{ $record->category == 'Senior' ? 'bg-orange-100 text-orange-700' : 
                              ($record->category == 'Child' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ $record->category }}
                        </span>
                    </td>

                    <td class="p-3">
                        <p class="font-medium">{{ $record->date_dispensed->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-400">{{ $record->created_at->format('h:i A') }}</p>
                    </td>

                    <td class="p-3 flex justify-center gap-2">
                        {{-- View Button --}}
                        <button type="button" class="view-medications-btn bg-blue-100 text-blue-700 p-2 rounded hover:bg-blue-600 hover:text-white transition">
                            <i class="fa-regular fa-eye mr-1"></i> View
                        </button>

                        {{-- Edit Button (Protected) --}}
                        @if (auth()->user()->user_level_id != 4 && (in_array(auth()->user()->user_level_id, [1, 2]) || auth()->user()->branch_id == $record->branch_id))
                            <button type="button" class="editrecordbtn bg-emerald-100 text-emerald-700 p-2 rounded hover:bg-emerald-600 hover:text-white transition">
                                <i class="fa-regular fa-pen-to-square mr-1"></i> Edit
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- PAGINATION --}}
{{-- IMPORTANT: The wrapper class "pagination-links" is required for JS --}}
<div class="p-4 border-t bg-white dark:bg-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4">
    <p class="text-sm text-gray-600">
        Showing {{ $patientrecords->firstItem() ?? 0 }} to {{ $patientrecords->lastItem() ?? 0 }} of {{ $patientrecords->total() }} results
    </p>
    <div class="pagination-links">
        {{ $patientrecords->links('pagination::tailwind') }}
    </div>
</div>