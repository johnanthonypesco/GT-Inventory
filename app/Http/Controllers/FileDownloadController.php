<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FileDownloadController extends Controller
{
    /**
     * Download the Staff/Admin APK file.
     * This is publicly accessible.
     */
    public function downloadApk()
    {
        $absolutePath = storage_path('app/staff/staff-rmpoims.apk');

        if (!File::exists($absolutePath)) {
            abort(404, 'The requested APK file could not be found.');
        }

        return response()->download($absolutePath, 'rmpoims-staff-app.apk', [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }

    /**
     * Download the Customer APK file.
     * This is now publicly accessible.
     */
    public function downloadCustomerApk()
    {
        $path = storage_path('app/customer/customer.apk');

        if (!File::exists($path)) {
            abort(404, 'Customer APK not found.');
        }

        return response()->download($path, 'customer.apk', [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }
}