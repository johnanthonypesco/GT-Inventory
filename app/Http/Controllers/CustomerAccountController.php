<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
                'password' => 'nullable|string|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])/|confirmed',
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
                    // ✅ Define upload directory (inside 'public/uploads/')
                    $uploadPath = 'uploads/profile_images/';
            
                    // ✅ Ensure the directory exists
                    if (!file_exists(public_path($uploadPath))) {
                        mkdir(public_path($uploadPath), 0775, true);
                    }
            
                    // ✅ Delete old profile image if exists
                    if (!empty($user->company->profile_image) && file_exists(public_path($uploadPath . basename($user->company->profile_image)))) {
                        unlink(public_path($uploadPath . basename($user->company->profile_image)));
                    }
            
                    // ✅ Store new image
                    $file = $request->file('profile_image');
                    $fileName = time() . '_' . $file->getClientOriginalName(); // Unique file name
                    $file->move(public_path($uploadPath), $fileName);
            
                    // ✅ Save path relative to 'uploads/' folder
                    $user->company->profile_image = $uploadPath . $fileName;
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