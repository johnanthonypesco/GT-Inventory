<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class MobileCustomerAccountController extends Controller
{
    public function getAccount()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'contact_number' => $user->contact_number,
                'profile_image' => $user->company ? asset('storage/' . $user->company->profile_image) : null,
            ]
        ]);
    }

    public function updateAccount(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:users,name,' . $user->id,
                'contact_number' => 'required|string|unique:users,contact_number,' . $user->id,
                'password' => 'nullable|min:8',
                'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            // Update user details
            $user->name = $validated['name'];
            $user->contact_number = $validated['contact_number'];

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                if ($user->company && $user->company->profile_image) {
                    Storage::delete('public/' . $user->company->profile_image);
                }
                $imagePath = $request->file('profile_image')->store('profile_images', 'public');
                if ($user->company) {
                    $user->company->profile_image = $imagePath;
                    $user->company->save();
                }
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Account updated successfully',
                'profile_image' => $user->company ? asset('storage/' . $user->company->profile_image) : null
            ]);

        } catch (\Exception $e) {
            \Log::error('Update Account Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Update failed!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
