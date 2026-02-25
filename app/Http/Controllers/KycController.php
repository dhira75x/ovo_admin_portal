<?php

namespace App\Http\Controllers;

use App\Models\KycSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class KycController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KycSubmission::with('user');

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        } else {
            // Default to showing pending first
            $query->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'approved' THEN 2 WHEN 'rejected' THEN 3 ELSE 4 END");
        }

        $submissions = $query->latest()->paginate(10);

        return view('admin.kyc.index', compact('submissions'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $submission = KycSubmission::with(['user', 'reviewer'])->findOrFail($id);
        return view('admin.kyc.show', compact('submission'));
    }

    /**
     * Approve the KYC submission.
     */
    public function approve($id)
    {
        try {
            $submission = KycSubmission::findOrFail($id);

            $submission->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'rejection_reason' => null, // Clear any previous rejection reason
            ]);

            return back()->with('success', 'KYC submission approved successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to approve submission: ' . $e->getMessage());
        }
    }

    /**
     * Reject the KYC submission.
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->with('error', 'Validation failed: ' . $validator->errors()->first());
        }

        try {
            $submission = KycSubmission::findOrFail($id);

            $submission->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            return back()->with('success', 'KYC submission rejected.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to reject submission: ' . $e->getMessage());
        }
    }
}
