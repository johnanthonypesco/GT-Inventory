<?php

// namespace App\Http\Controllers\Mobile;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\Staff;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Mail;
// use App\Mail\TwoFactorCodeMail;

// class MobileStaffAuthController extends Controller
// {
//     public function login(Request $request)
//     {
//         $request->validate(['email' => 'required|email', 'password' => 'required']);
//         $staff = Staff::where('email', $request->email)->first();

//         if (!$staff || !Hash::check($request->password, $staff->password)) {
//             return response()->json(['message' => 'Invalid credentials.'], 401);
//         }

//         $staff->generateTwoFactorCode();
//         Mail::to($staff->email)->send(new TwoFactorCodeMail($staff->two_factor_code));
//         return response()->json(['two_factor_user_id' => $staff->id]);
//     }

//     public function verifyTwoFactor(Request $request)
//     {
//         $request->validate([
//             'user_id' => 'required|exists:staff,id',
//             'code' => 'required|digits:6',
//         ]);

//         $staff = Staff::find($request->user_id);

//         if ($staff->two_factor_code !== (int)$request->code || now('Asia/Manila')->gt($staff->two_factor_expires_at)) {
//             return response()->json(['message' => 'Invalid or expired code.'], 403);
//         }

//         $staff->two_factor_code = null;
//         $staff->two_factor_expires_at = null;
//         $staff->save();

//         $token = $staff->createToken('StaffMobileApp')->plainTextToken;
//         return response()->json(['token' => $token, 'user' => $staff]);
//     }

//     /**
//      * ✅ FIX: This method now returns a structured JSON object
//      * with the specific fields you need, like 'id' and 'staff_username'.
//      */
//     public function user(Request $request)
//     {
//         $staff = $request->user();

//         return response()->json([
//             'id' => $staff->id,
//             'staff_username' => $staff->staff_username,
//             'email' => $staff->email,
//             'job_title' => $staff->job_title,
//         ]);
//     }

//     public function logout(Request $request)
//     {
//         $request->user()->currentAccessToken()->delete();
//         return response()->json(['message' => 'Logged out successfully.']);
//     }
// }

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class MobileStaffAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $staff = Staff::where('email', $request->email)->first();

        if (!$staff || !Hash::check($request->password, $staff->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $staff->generateTwoFactorCode();
        Mail::to($staff->email)->send(new TwoFactorCodeMail($staff->two_factor_code));
        return response()->json(['two_factor_user_id' => $staff->id]);
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:staff,id',
            'code' => 'required|string|digits:6', // Tiyaking string ang validation
        ]);

        $staff = Staff::find($request->user_id);

        if (!$staff) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        /**
         * ✅ ANG FIX: Inalis ang (int) para string-to-string comparison
         */
        if ($staff->two_factor_code !== $request->code || now('Asia/Manila')->gt($staff->two_factor_expires_at)) {
            return response()->json(['message' => 'Invalid or expired code.'], 403);
        }

        $staff->two_factor_code = null;
        $staff->two_factor_expires_at = null;
        $staff->save();

        $token = $staff->createToken('StaffMobileApp')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $staff]);
    }

    public function user(Request $request)
    {
        $staff = $request->user();
        return response()->json([
            'id' => $staff->id,
            'staff_username' => $staff->staff_username,
            'email' => $staff->email,
            'job_title' => $staff->job_title,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }
}