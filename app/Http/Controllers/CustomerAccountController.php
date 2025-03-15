<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CustomerAccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        try {
            $user->password = Hash::decrypt($user->password);
        } catch (\Exception $e) {
            $user->password = "Unable to decrypt";
        }

        return view('customer.account', ['user' => $user]);
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();
    
            // Validate input
          $validated =  $request->validate([
                'name' => 'required|string|max:255|unique:users,name,' . $user->id,
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'contact_number' => 'required|string|unique:users,contact_number,' . $user->id,
                'password' => 'nullable|min:8',
                'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // ✅ New rule for profile image
            ]);
            $validated = array_map("strip_tags", $validated);

            // Assign values
            $user->name = $request->name;
            $user->email = $request->email;
            $user->contact_number = $request->contact_number;
    
            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($user->profile_image) {
                    Storage::delete('public/' . $user->profile_image);
                }
    
// Store new image
$imagePath = $request->file('profile_image')->store('profile_images', 'public');

// Ensure the user has a company before updating
if ($user->company) {
    $company = $user->company; // Fetch the company model
    $company->profile_image = $imagePath; // Update profile image
    $company->save(); // ✅ Save the company with the new profile image
}
            }
    
            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
    
            $user->save();
    
            return response()->json(['success' => true, 'message' => 'Account updated successfully', 'image' => asset('storage/' . $user->profile_image)]);
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