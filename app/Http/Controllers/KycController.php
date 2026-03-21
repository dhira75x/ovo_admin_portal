<?php

namespace App\Http\Controllers;

use App\Services\EcommerceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class KycController extends Controller
{
    protected $service;

    public function __construct(EcommerceService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = [
            'page' => $request->get('page', 1),
            'limit' => 10,
            'status' => $request->get('status'),
        ];

        $response = $this->service->getKycSubmissions(array_filter($params));
        $payload = $response['payload'] ?? [];
        
        $submissions = new \Illuminate\Pagination\LengthAwarePaginator(
            $payload['docs'] ?? [],
            $payload['totalDocs'] ?? 0,
            $payload['limit'] ?? 10,
            $payload['page'] ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.kyc.index', compact('submissions'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $response = $this->service->getKycSubmissions();
        $submissions = $response['payload']['docs'] ?? [];
        $submission = collect($submissions)->firstWhere('_id', $id);

        if (!$submission) {
            abort(404);
        }

        return view('admin.kyc.show', compact('submission'));
    }

    /**
     * Approve the KYC submission.
     */
    public function approve($id)
    {
        try {
            $response = $this->service->updateKycStatus($id, 'approved');

            if ($response && ($response['status'] ?? '') === 'OK') {
                return back()->with('success', 'KYC submission approved successfully.');
            }

            $message = $response['message'] ?? 'Failed to approve submission';
            return back()->with('error', $message);
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
            $response = $this->service->updateKycStatus($id, 'rejected', $request->rejection_reason);

            if ($response && ($response['status'] ?? '') === 'OK') {
                return back()->with('success', 'KYC submission rejected.');
            }

            $message = $response['message'] ?? 'Failed to reject submission';
            return back()->with('error', $message);
        } catch (Exception $e) {
            return back()->with('error', 'Failed to reject submission: ' . $e->getMessage());
        }
    }
}
