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
            $user = auth()->user();
            
            // Validate input
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'contact_number' => 'nullable|string|max:20',
                'password' => 'nullable|min:8|confirmed',
                'password_confirmation' => 'nullable|same:password',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            
            // Update user fields
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->contact_number = $validatedData['contact_number'] ?? $user->contact_number;
            
            // Update password only if provided
            if (!empty($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }
            
            $user->save();
            
            // Handle profile image update
            if ($request->hasFile('profile_image')) {
                if ($user->company) {
                    // Delete old profile image if exists
                    if (!empty($user->company->profile_image) && Storage::exists('public/' . $user->company->profile_image)) {
                        Storage::delete('public/' . $user->company->profile_image);
                    }
                    
                    // Store new image
                    $imagePath = $request->file('profile_image')->store('profile_images', 'public');
                    $user->company->profile_image = $imagePath;
                    $user->company->save();
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'No associated company profile found!',
                    ], 400);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Account updated successfully!'
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Update Account Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Update failed!',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
    
}    