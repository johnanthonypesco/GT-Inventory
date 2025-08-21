{{-- resources/views/admin/partials/accounts_table.blade.php --}}

<div class="table-container mt-5 overflow-auto">
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Account ID</th>
                <th class="py-2 px-4 border-b">Name/Username</th>
                <th class="py-2 px-4 border-b">Email Address</th>
                <th class="py-2 px-4 border-b">Role</th>
                <th class="py-2 px-4 border-b">Company</th>
                <th class="py-2 px-4 border-b">Action</th>
            </tr>
        </thead>
        <tbody id="accountsTableBody">
            @php
                $isSuperAdmin = auth()->guard('superadmin')->check();
                $isAdmin = auth()->guard('admin')->check();
            @endphp

            @forelse ($accounts as $account)
                @if($isSuperAdmin || ($isAdmin && in_array($account->role, ['staff', 'customer'])))
                <tr
                    data-id="{{ $account->id }}"
                    data-name="{{ $account->name }}"
                    data-username="{{ $account->username ?? $account->staff_username ?? '' }}"
                    data-email="{{ $account->email }}"
                    data-role="{{ $account->role }}"
                    data-contactnumber="{{ $account->contact_number ?? 'N/A' }}">

                    <td class="py-2 px-4 border-b text-center">{{ $account->id }}</td>
                    <td class="py-2 px-4 border-b">{{ $account->name ?? $account->username ?? $account->staff_username ?? 'N/A' }}</td>
                    <td class="py-2 px-4 border-b">{{ $account->email }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ ucfirst($account->role) }}</td>
                    
                    {{-- *** ITO ANG BINAGO: Idinagdag ang logic para sa Company Name *** --}}
                    <td class="py-2 px-4 border-b text-center">
                        @if ($account->role === 'customer' && $account->company_id && isset($companies[$account->company_id]))
                            {{ $companies[$account->company_id]->name }}
                        @elseif ($account->role !== 'customer')
                            RCT Med Pharma
                        @else
                            N/A
                        @endif
                    </td>

                    <td class="py-2 px-4 border-b flex justify-center items-center gap-4 font-bold">
                        <button class="text-[#005382] cursor-pointer bg-[#005382]/20 p-2 rounded-lg hover:text-white hover:bg-[#005382] hover:-translate-y-1 transition-all duration-200 flex gap-1 items-center" onclick="openEditAccountModal(this)">
                            <i class="fa-regular fa-pen-to-square mr-2"></i> Edit
                        </button>
                        <form id="deleteaccountform-{{ $account->id }}" method="POST" action="{{ route('superadmin.account.delete', ['role' => $account->role, 'id' => $account->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="deleteaccountbtn text-red-500 cursor-pointer bg-red-600/20 p-2 rounded-lg hover:text-white hover:bg-red-600 hover:-translate-y-1 transition-all duration-200 flex gap-1 items-center"
                                data-account-id="{{ $account->id }}"
                                onclick="confirmDelete(this)">
                                <i class="fa-solid fa-trash mr-2"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endif
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No accounts found matching your criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $accounts->links() }}
</div>