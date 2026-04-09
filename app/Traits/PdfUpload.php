<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait PdfUpload
{
    function save_pdf($request_pdf, $folder_name, $oldFilePath)
{
    try {
        // Ensure the folder exists
        if (!Storage::disk('public')->exists($folder_name)) {
            Storage::disk('public')->makeDirectory($folder_name);
        }

        // Check if the file is a PDF
        if ($request_pdf && $request_pdf->getClientOriginalExtension() === 'pdf') {
            // Delete the old file if it exists
            if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
            }
            // Save the new PDF
            $uniqueKey = substr(str_shuffle(uniqid()), 0, 5);
            $filename =  $request_pdf->getClientOriginalName().'_'.$uniqueKey. '.pdf';
            $relativePath = $folder_name . '/' . $filename;
            Storage::disk('public')->putFileAs($folder_name, $request_pdf, $filename);
            return $relativePath;
        } else {
            return response()->json(['message' => "Please upload a valid PDF file.", 'status' => false], 400);
        }
    } catch (\Throwable $th) {
        return response()->json(['message' => "Something went wrong...! Contact your admin...", 'status' => false], 500);
    }
}

}
