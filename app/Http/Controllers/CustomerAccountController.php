<?php

namespace App\Http\Controllers;

use Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException; // It's good practice to catch validation exceptions specifically

class CustomerAccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // This part is for display only and generally not recommended.
        // It's better to just have the user enter a new password without showing the old one.
        // However, leaving as is since it's not the source of the update error.
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
            
            // Define custom messages for validation rules
            $messages = [
                'password.min' => 'The password must be at least 8 characters.',
                'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&_).',
                'password.confirmed' => 'The password confirmation does not match.',
                'contact_number.digits' => 'The contact number must be exactly 11 digits.'
            ];

            // FIX: Pass the custom messages array as the second argument to the validate method
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'contact_number' => 'nullable|numeric|digits:11',
                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&_])/',
                    'confirmed'
                ],
                'password_confirmation' => 'nullable|same:password',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], $messages);
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
                    $uploadSubfolder = 'uploads/profile_images';

                    $targetDir = App::environment('local') 
                        ? public_path($uploadSubfolder) 
                        : base_path('../public_html/' . $uploadSubfolder);

                    if (!file_exists($targetDir)) {
                        mkdir($targetDir, 0775, true);
                    }

                    // Delete old profile image if it exists
                    $oldImage = $user->company->profile_image ?? null;
                    if (!empty($oldImage) && file_exists(public_path($oldImage))) {
                        unlink(public_path($oldImage));
                    }

                    // Store new image
                    $file = $request->file('profile_image');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move($targetDir, $fileName);

                    // Save path relative to the public folder
                    $user->company->profile_image = $uploadSubfolder . '/' . $fileName;
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

        } catch (ValidationException $e) {
            // Return validation errors specifically
            return response()->json([
                'success' => false,
                'message' => 'Validation failed!',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Update Account Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Update failed!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}