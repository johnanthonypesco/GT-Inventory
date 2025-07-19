<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Http\Requests\UpdateAccountRequest; // Import the new request class
use Illuminate\Support\Facades\Log; // Import Log facade for better error handling

class MobileCustomerAccountController extends Controller
{
    /**
     * Fetch the authenticated user's account details.
     */
    public function getAccount(Request $request)
    {
        $user = $request->user();

        // Eager load the company relationship to avoid extra queries
        $user->load('company');

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'contact_number' => $user->contact_number,
                // Check if company and profile_image exist before creating the asset URL
                'profile_image' => ($user->company && $user->company->profile_image)
                    ? asset('storage/' . $user->company->profile_image)
                    : null,
            ]
        ]);
    }

    /**
     * Update the authenticated user's account.
     * Use the custom UpdateAccountRequest for validation.
     */
    public function updateAccount(UpdateAccountRequest $request)
    {
        try {
            // The request is already validated by UpdateAccountRequest
            $validated = $request->validated();
            $user = $request->user();

            // Update user details
            $user->name = $validated['name'];
            $user->contact_number = $validated['contact_number'];

            // Update password only if a new one is provided
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            
            $user->save();

            // Handle profile image upload if it exists in the request
            if ($request->hasFile('profile_image')) {
                // Ensure the user has a company relationship to update
                if ($user->company) {
                    // Delete the old image if it exists
                    if ($user->company->profile_image) {
                        Storage::disk('public')->delete($user->company->profile_image);
                    }
                    
                    // Store the new image and update the path
                    $imagePath = $request->file('profile_image')->store('profile_images', 'public');
                    $user->company->profile_image = $imagePath;
                    $user->company->save();
                }
            }

            // Eager load the relationship again to get the updated path
            $user->load('company');
            
            return response()->json([
                'success' => true,
                'message' => 'Account updated successfully.',
                // Return the new image URL
                'user' => [
                    'name' => $user->name,
                    'contact_number' => $user->contact_number,
                    'profile_image' => ($user->company && $user->company->profile_image)
                        ? asset('storage/' . $user->company->profile_image)
                        : null,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // This will catch validation errors if any slip through or for debugging
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Update Account Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}