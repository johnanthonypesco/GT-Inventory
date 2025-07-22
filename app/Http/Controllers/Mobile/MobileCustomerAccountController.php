<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;  // Add this
use Illuminate\Support\Facades\File; // Add this

class MobileCustomerAccountController extends Controller
{
    /**
     * Fetch the authenticated user's account details.
     */
    public function getAccount(Request $request)
    {
        $user = $request->user();
        $user->load('company');

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'contact_number' => $user->contact_number,
                // ✅ Use url() helper for correct URL generation
                'profile_image' => ($user->company && $user->company->profile_image)
                    ? url($user->company->profile_image)
                    : null,
            ]
        ]);
    }

    /**
     * Update the authenticated user's account.
     */
    public function updateAccount(UpdateAccountRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $request->user();

            // Update user details
            $user->name = $validated['name'];
            $user->contact_number = $validated['contact_number'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            
            $user->save();

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                if ($user->company) {
                    // ✅ Dynamically delete the old image
                    if ($user->company->profile_image) {
                        $oldPath = $user->company->profile_image;
                        $deletePath = App::environment('local') 
                            ? public_path($oldPath) 
                            : base_path('../public_html/' . $oldPath);
                        if (File::exists($deletePath)) {
                            File::delete($deletePath);
                        }
                    }
                    
                    // ✅ Dynamically store the new image
                    $file = $request->file('profile_image');
                    $fileName = $file->hashName();
                    $subfolder = 'profile_images';

                    $targetDir = App::environment('local') 
                        ? public_path($subfolder) 
                        : base_path('../public_html/' . $subfolder);

                    if (!File::exists($targetDir)) {
                        File::makeDirectory($targetDir, 0755, true);
                    }

                    $file->move($targetDir, $fileName);
                    
                    // Store the new relative path
                    $user->company->profile_image = $subfolder . '/' . $fileName;
                    $user->company->save();
                }
            }

            $user->load('company');
            
            return response()->json([
                'success' => true,
                'message' => 'Account updated successfully.',
                'user' => [
                    'name' => $user->name,
                    'contact_number' => $user->contact_number,
                    // ✅ Use url() helper for correct URL generation
                    'profile_image' => ($user->company && $user->company->profile_image)
                        ? url($user->company->profile_image)
                        : null,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
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