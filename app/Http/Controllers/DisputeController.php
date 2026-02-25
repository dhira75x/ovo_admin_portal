<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class DisputeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Dispute::with(['claimant', 'respondent']);

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $disputes = $query->latest()->paginate(10);

        return view('admin.disputes.index', compact('disputes'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $dispute = Dispute::with(['claimant', 'respondent', 'resolvedBy', 'evidence.uploader'])->findOrFail($id);
        
        $evidence = $dispute->evidence;

        return view('admin.disputes.show', compact('dispute', 'evidence'));
    }

    /**
     * Resolve the dispute.
     */
    public function resolve(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'winner_id' => 'required', // Could be claimant or respondent ID, or distinct value
            'resolution_notes' => 'required|string',
            'status' => 'required|in:resolved,closed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->with('error', 'Validation failed: ' . $validator->errors()->first());
        }

        try {
            $dispute = Dispute::findOrFail($id);
            
            $dispute->update([
                'status' => $request->status,
                'resolution_notes' => $request->resolution_notes,
                'resolved_by' => Auth::id(), // Assuming admin guard
                'resolved_at' => now(),
            ]);
            
            // Logic to award/refund would go here (omitted for now)

            return back()->with('success', 'Dispute resolved successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to resolve dispute: ' . $e->getMessage());
        }
    }
}
