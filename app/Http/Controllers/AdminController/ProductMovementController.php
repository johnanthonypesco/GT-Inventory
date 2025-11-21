<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductMovement;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class ProductMovementController extends Controller
{
    public function showMovements(Request $request)
    {
        $search = $request->input('search', '');
        $product_id = $request->input('product_id', '');
        $type = $request->input('type', '');
        $user_id = $request->input('user_id', '');
        $branch_id = $request->input('branch_id', ''); // NEW: Branch filter
        $from = $request->input('from', '');
        $to = $request->input('to', '');

        $sort = $request->input('sort', 'desc');

        $query = ProductMovement::with(['product', 'user', 'inventory'])
            ->orderBy('created_at', $sort);

        // Search by description or batch number
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('inventory', fn($q_inv) => $q_inv->where('batch_number', 'like', "%{$search}%"));
            });
        }

        if (!empty($product_id)) $query->where('product_id', $product_id);
        if (!empty($type)) $query->where('type', $type);
        if (!empty($user_id)) $query->where('user_id', $user_id);

        // NEW: Filter by branch
        if (!empty($branch_id)) {
            $query->whereHas('inventory', fn($q) => $q->where('branch_id', $branch_id));
        }

        // Date range
        if (!empty($from) && !empty($to)) {
            $query->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        } elseif (!empty($from)) {
            $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        } elseif (!empty($to)) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        $movements = $query->paginate(20)->withQueryString();

        // Stats for cards (Today)
        $today = Carbon::today();
        $movementsTodayCount = ProductMovement::whereDate('created_at', $today)->count();
        $itemsInToday = ProductMovement::where('type', 'IN')->whereDate('created_at', $today)->sum('quantity');
        $itemsOutToday = ProductMovement::where('type', 'OUT')->whereDate('created_at', $today)->sum('quantity');

        $products = Product::where('is_archived', 1)->orderBy('generic_name')->get();
        $users = User::orderBy('name')->get();

        if ($request->ajax()) {
            return view('admin.partials._movements_table', compact('movements'))->render();
        }

        return view('admin.product_movements', compact(
            'movements',
            'products',
            'users',
            'movementsTodayCount',
            'itemsInToday',
            'itemsOutToday'
        ));
    }
}