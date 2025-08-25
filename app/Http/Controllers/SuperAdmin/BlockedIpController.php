<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\BlockedIp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class BlockedIpController extends Controller
{
    /**
     * Display a list of all blocked IPs.
     */
    public function index(): View
    {
        $blockedIps = BlockedIp::orderBy('created_at', 'desc')->paginate(10);
        return view('superadmin.blocked-ips', compact('blockedIps'));
    }

    /**
     * Handle AJAX search requests for blocked IPs.
     */
    public function search(Request $request): View
    {
        $query = BlockedIp::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('ip_address', 'like', '%' . $search . '%')
                  ->orWhere('reason', 'like', '%' . $search . '%');
        }

        $blockedIps = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.partials.blocked_ips_table', compact('blockedIps'));
    }

    /**
     * Unblock an IP by deleting it from the database.
     */
    public function destroy(BlockedIp $blockedIp): RedirectResponse
    {
        $blockedIp->delete();
        return back()->with('success', "IP address {$blockedIp->ip_address} has been unblocked.");
    }
}