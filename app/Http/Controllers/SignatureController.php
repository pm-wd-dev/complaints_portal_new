<?php

namespace App\Http\Controllers;

use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'signature' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'signature_id' => 'required|exists:signatures,id'
        ]);

        try {
            $signature = Signature::findOrFail($request->signature_id);
            
            // Delete old signature if exists
            if ($signature->signature_path) {
                Storage::delete($signature->signature_path);
            }

            // Store new signature
            $path = $request->file('signature')->store('signatures', 'public');
            $signature->signature_path = $path;
            $signature->save();

            return response()->json([
                'message' => 'Signature uploaded successfully',
                'path' => Storage::url($path)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error uploading signature'
            ], 500);
        }
    }
}
