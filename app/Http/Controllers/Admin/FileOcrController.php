<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ArchivesOcrReceipt;

class FileOcrController extends Controller
{
    /**
     * Ipakita ang root page ng OCR Archives, na may filter para alisin ang base 'receipts' folder.
     */
    public function index()
    {
        $imagePaths = ArchivesOcrReceipt::pluck('image_path');

        $imageDirectories = $imagePaths
            ->map(function ($path) {
                // Get the directory name (e.g., 'receipts' or 'receipts/2025/08/13')
                return dirname($path);
            })
            ->unique()
            // I-filter out ang directory kung ito ay 'receipts' lamang.
            ->filter(function ($directory) {
                return $directory !== 'receipts';
            })
            ->sort()
            ->values();

        // Ang logic para sa DOCX ay tama na at hindi kailangan baguhin.
        $allDocxFiles = collect(Storage::disk('public')->allFiles('receipts/docs_ocr_copy'));
        $docxDirectories = $allDocxFiles
            ->filter(fn($file) => Str::endsWith($file, '.docx'))
            ->map(fn($file) => dirname($file))
            ->unique()
            ->sort()
            ->values();

        return view('admin.file-ocr', [
            'imageDirectories' => $imageDirectories,
            'docxDirectories'  => $docxDirectories,
        ]);
    }

    /**
     * Kunin ang laman ng isang specific folder via AJAX.
     */
    public function getFolderContents(Request $request)
    {
        $validated = $request->validate([
            'path' => 'required|string',
            'type' => 'required|in:image,docx',
        ]);

        $path = $validated['path'];
        $type = $validated['type'];

        if (!Str::startsWith($path, 'receipts/')) {
            return response()->json(['error' => 'Invalid path.'], 403);
        }

        $urls = [];

        if ($type === 'image') {
            $imagePaths = ArchivesOcrReceipt::where('image_path', 'like', $path . '/%')->pluck('image_path');
            // ✅ FIX: Use the asset() helper to generate the correct public URL
            // instead of Storage::url().
            $urls = $imagePaths->map(fn($file) => asset($file));
        } elseif ($type === 'docx') {
            // This part is likely correct if DOCX files are still managed via the storage disk
            $files = Storage::disk('public')->files($path);
            $urls = collect($files)->map(fn($file) => Storage::url($file));
        }

        return response()->json(['files' => $urls]);
    }
}
// 9e04c8a636c02bec62f8e0b4d14d49cdc5f50453