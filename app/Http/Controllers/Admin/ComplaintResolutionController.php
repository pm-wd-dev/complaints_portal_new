<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\CaseSignature;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ComplaintResolutionController extends Controller
{
    public function getSignatureStatus(Complaint $complaint)
    {
        $complaint->load(['latestResolution.signatures.user']);
        
        if (!$complaint->latestResolution) {
            return response()->json([
                'total_required' => 0,
                'total_signed' => 0,
                'status' => 'no_resolution',
                'signatures' => []
            ]);
        }

        $signatures = $complaint->latestResolution->signatures;
        $totalRequired = $signatures->count();
        $totalSigned = $signatures->where('signature_path', '!=', null)->count();
        
        $signatureDetails = $signatures->map(function($signature) {
            return [
                'role' => $signature->signer_role,
                'name' => $signature->signer_name,
                'signed' => $signature->signature_path !== null,
                'signed_at' => $signature->signature_path ? $signature->updated_at->format('Y-m-d H:i:s') : null
            ];
        });

        return response()->json([
            'total_required' => $totalRequired,
            'total_signed' => $totalSigned,
            'status' => $totalSigned === $totalRequired ? 'completed' : 'pending',
            'signatures' => $signatureDetails
        ]);
    }
    public function preview(Request $request, Complaint $complaint)
    {
        // Eager load necessary relationships
        $complaint->load(['latestResolution.signatures.user', 'respondents.user']);

        $validated = $request->validate([
            'resolution_text' => 'required|string',
            'template_type' => 'required|string|in:standard,detailed,summary',
            'signers' => 'required|array',
            'signers.*' => 'required|string|in:complainant,respondent,leadership'
        ]);

        // Generate PDF
        $pdf = PDF::loadView('admin.pdfs.resolution', [
            'complaint' => $complaint,
            'resolution_text' => $validated['resolution_text'],
            'template_type' => $validated['template_type'],
            'signers' => $validated['signers'],
            'generated_at' => now()->format('F d, Y'),
        ]);

        return $pdf->stream('resolution-preview.pdf');
    }

    public function generate(Request $request, Complaint $complaint)
    {
    // Eager load necessary relationships
    $complaint->load(['latestResolution.signatures.user', 'respondents.user']);

    $validated = $request->validate([
        'resolution_text' => 'required|string',
        'template_type' => 'required|string|in:standard,detailed,summary',
        'signers' => 'required|array',
        'signers.*' => 'required|string|in:complainant,respondent,leadership',
        'signer_names' => 'required|array',
        'signer_emails' => 'required|array',
    ]);

    \DB::beginTransaction();

    $filename = null;
    $pdfPath = public_path('images/complaints/resolutions');

    try {
        // Ensure directory exists
        if (!file_exists($pdfPath)) {
            mkdir($pdfPath, 0755, true);
        }

        // Generate PDF
        $pdf = PDF::loadView('admin.pdfs.resolution', [
            'complaint' => $complaint,
            'resolution_text' => $validated['resolution_text'],
            'template_type' => $validated['template_type'],
            'signers' => $validated['signers'],
            'generated_at' => now()->format('F d, Y'),
        ])->setPaper('a4');

        // Save PDF
        $filename = 'resolution_' . $complaint->case_number . '_' . time() . '.pdf';
        $pdf->save($pdfPath . '/' . $filename);

        // Create resolution record
        $resolution = $complaint->resolutions()->create([
            'resolution_text' => $validated['resolution_text'],
            'generated_pdf_path' => 'images/complaints/resolutions/' . $filename,
            'template_type' => $validated['template_type'],
            'generated_by' => auth()->id(),
        ]);

        // Update complaint status
        $complaint->status = 'resolved';
        $complaint->awaiting_signature = true;
        $complaint->save();

        // Create signer entries
        foreach ($validated['signers'] as $index => $role) {
            $signerName = $validated['signer_names'][$index] ?? null;
            $signerEmail = $validated['signer_emails'][$index] ?? null;

            if (!$signerName || !$signerEmail) {
                throw new \Exception("Signer name or email missing for role: $role");
            }

            $user = User::where('email', $signerEmail)->first();

            $resolution->signatures()->create([
                'complaint_id' => $complaint->id,
                'user_id' => $user?->id,
                'signer_name' => $signerName,
                'signer_email' => $signerEmail,
                'signer_role' => $role,
                'ip_address' => $request->ip(),
            ]);
        }

        \DB::commit();

        return response()->json([
            'message' => 'Resolution document has been generated and sent for signatures.',
            'resolution_id' => $resolution->id,
        ]);

    } catch (\Throwable $e) {
        \DB::rollBack();

        // Clean up PDF if it exists
        if ($filename && file_exists($pdfPath . '/' . $filename)) {
            unlink($pdfPath . '/' . $filename);
        }

        \Log::error('Resolution Generation Failed', [
            'complaint_id' => $complaint->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'Failed to generate resolution document.',
            'details' => $e->getMessage(),
        ], 500);
    }
}
    public function uploadSignature(Request $request)
    {
        \Log::info('Signature upload initiated', [
            'user_id' => auth()->id(),
            'signature_id' => $request->signature_id
        ]);

        try {
            $validation = $request->validate([
                'signature' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'signature_id' => 'required|exists:case_signatures,id'
            ]);

            \Log::info('Signature validation passed', [
                'file_size' => $request->file('signature')->getSize(),
                'file_type' => $request->file('signature')->getMimeType()
            ]);

            $signature = CaseSignature::findOrFail($request->signature_id);
            
            // Delete old signature if exists
            if ($signature->signature_path && file_exists(public_path($signature->signature_path))) {
                \Log::info('Deleting old signature', ['old_path' => $signature->signature_path]);
                unlink(public_path($signature->signature_path));
            }

            // Create signatures directory if it doesn't exist
            $path = public_path('images/signatures');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Store new signature
            $file = $request->file('signature');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($path, $filename);
            
            // Set the relative path for database storage
            $filePath = 'images/signatures/' . $filename;
            \Log::info('New signature stored', ['new_path' => $filePath]);

            $signature->signature_path = $filePath;
            $signature->signed_at = now();
            $signature->save();

            // Check if all signatures are complete and generate PDF
            $this->checkAndGenerateResolutionPdf($signature->complaint_id);

            \Log::info('Signature upload completed successfully', [
                'signature_id' => $signature->id,
                'path' => $path
            ]);

            return response()->json([
                'message' => 'Signature uploaded successfully',
                'path' => Storage::url($path)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Signature validation failed', [
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Signature upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error uploading signature'
            ], 500);
        }
    }

    private function checkAndGenerateResolutionPdf($complaintId)
    {
        try {
            \Log::info('Starting resolution PDF generation check', ['complaint_id' => $complaintId]);
            
            $complaint = Complaint::with([
                'latestResolution.signatures.user',
                'respondents.user'
            ])->find($complaintId);
            \Log::info('Complaint loaded', [
                'case_number' => $complaint->case_number,
                'status' => $complaint->status,
                'resolution_text' => $complaint->latestResolution->resolution_text,
                'signatures_count' => $complaint->signatures->count(),
                'respondents_count' => $complaint->respondents->count()
            ]);
            
            // Get all required signatures for this complaint
            $requiredSignatures = collect($complaint->signatures->pluck('signer_role')->toArray() ?? ['complainant', 'respondent', 'leadership']);
            \Log::info('Required signatures', ['signers' => $requiredSignatures->toArray()]);
            
            // Update complaint status based on signatures
            $complaint->awaiting_signature = true;
            $complaint->save();
            
            $allSignaturesComplete = true;
            
            foreach ($requiredSignatures as $signer) {
                $signature = null;
                
                if ($signer === 'complainant') {
                    $signature = $complaint->signatures()->whereNull('user_id')->first();
                    \Log::info('Checking complainant signature', [
                        'found' => $signature ? 'yes' : 'no',
                        'has_path' => $signature && $signature->signature_path ? 'yes' : 'no'
                    ]);
                } elseif ($signer === 'respondent') {
                    $respondent = $complaint->respondents->first();
                    \Log::info('Checking respondent', [
                        'respondent_id' => $respondent ? $respondent->user_id : 'none'
                    ]);
                    $signature = $respondent ? $complaint->signatures()->where('user_id', $respondent->user_id)->first() : null;
                    \Log::info('Checking respondent signature', [
                        'found' => $signature ? 'yes' : 'no',
                        'has_path' => $signature && $signature->signature_path ? 'yes' : 'no'
                    ]);
                } elseif ($signer === 'leadership') {
                    $signature = $complaint->signatures()->whereExists(function ($query) {
                        $query->from('users')
                              ->whereColumn('users.id', 'case_signatures.user_id')
                              ->where('users.role', 'admin');
                    })->first();
                    \Log::info('Checking leadership signature', [
                        'found' => $signature ? 'yes' : 'no',
                        'has_path' => $signature && $signature->signature_path ? 'yes' : 'no'
                    ]);
                }
                
                if (!$signature || !$signature->signature_path) {
                    $allSignaturesComplete = false;
                    \Log::info('Missing required signature', ['signer' => $signer]);
                    break;
                }
            }
            
            // If all signatures are complete, generate the PDF
            \Log::info('Signature check complete', [
                'all_complete' => $allSignaturesComplete ? 'yes' : 'no',
                'complaint_id' => $complaintId
            ]);
            
            if ($allSignaturesComplete) {
                \Log::info('Starting PDF generation', [
                    'complaint_id' => $complaintId,
                    'resolution_text' => $complaint->latestResolution->resolution_text,
                    'signers' => $requiredSignatures->toArray()
                ]);
                
                $pdf = PDF::loadView('admin.pdfs.resolution', [
                    'complaint' => $complaint,
                    'resolution_text' => $complaint->latestResolution->resolution_text,
                    'signers' => $requiredSignatures->toArray(),
                    'generated_at' => now()->format('F d, Y'),
                    'template_type'=>$complaint->latestResolution->template_type
                ]);
                
                // Create resolutions directory if it doesn't exist
                $path = public_path('documents/resolutions');
                \Log::info('Checking resolutions directory', [
                    'path' => $path,
                    'exists' => file_exists($path) ? 'yes' : 'no'
                ]);

                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                    \Log::info('Created resolutions directory', ['path' => $path]);
                }
                
                // Generate filename and save PDF
                $filename = 'resolution_' . $complaint->case_number . '_' . now()->format('Ymd_His') . '.pdf';
                $fullPath = public_path('documents/resolutions/' . $filename);
                
                try {
                    $pdf->save($fullPath);
                    \Log::info('PDF saved successfully', [
                        'filename' => $filename,
                        'full_path' => $fullPath,
                        'file_exists' => file_exists($fullPath) ? 'yes' : 'no',
                        'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to save PDF', [
                        'filename' => $filename,
                        'error' => $e->getMessage(),
                        'path' => $fullPath
                    ]);
                    throw $e;
                }
                
                // Update resolution record with PDF path
                $resolution = $complaint->resolutions()->latest()->first();
                \Log::info('Found resolution record', [
                    'found' => $resolution ? 'yes' : 'no',
                    'resolution_id' => $resolution ? $resolution->id : null
                ]);
                
                if ($resolution) {
                    $resolution->generated_pdf_path = 'documents/resolutions/' . $filename;
                    $resolution->save();
                    \Log::info('Updated resolution record with PDF path', [
                        'resolution_id' => $resolution->id,
                        'pdf_path' => $resolution->generated_pdf_path
                    ]);
                }

                // Update complaint status to show signatures are complete
                $complaint->awaiting_signature = false;
                $complaint->save();
                
                \Log::info('Resolution PDF generated successfully', [
                    'complaint_id' => $complaintId,
                    'filename' => $filename
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error generating resolution PDF: ' . $e->getMessage(), [
                'complaint_id' => $complaintId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
