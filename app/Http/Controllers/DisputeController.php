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
        $dispute = Dispute::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'winner_id' => 'required', // Could be claimant or respondent ID, or distinct value
            'resolution_notes' => 'required|string',
            'status' => 'required|in:resolved,closed',
            'issue_refund' => 'nullable|boolean',
            'refund_amount' => [
                'nullable',
                'required_if:issue_refund,true',
                'required_if:issue_refund,1',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($dispute) {
                    if ($dispute->dispute_amount && $value > $dispute->dispute_amount) {
                         $fail('The refund amount cannot exceed the dispute amount ($' . number_format($dispute->dispute_amount, 2) . ').');
                    }
                },
            ],
            'refund_transaction_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->with('error', 'Validation failed: ' . $validator->errors()->first())->withInput();
        }

        try {
            $updateData = [
                'status' => $request->status,
                'resolution_notes' => $request->resolution_notes,
                'resolved_by' => Auth::id(), // Assuming admin guard
                'resolved_at' => now(),
            ];

            if ($request->boolean('issue_refund')) {
                $updateData['is_refunded'] = true;
                $updateData['refund_amount'] = $request->refund_amount;
                $updateData['refund_transaction_id'] = $request->refund_transaction_id;
                $updateData['refunded_at'] = now();
            }

            $dispute->update($updateData);
            
            // Logic to award/refund would go here (omitted for now)

            return back()->with('success', 'Dispute resolved successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to resolve dispute: ' . $e->getMessage());
        }
    }
}
