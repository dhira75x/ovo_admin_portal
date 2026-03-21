<?php

namespace App\Http\Controllers;

use App\Services\EcommerceService;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    protected $service;

    public function __construct(EcommerceService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of merchants.
     */
    public function index(Request $request)
    {
        $params = [
            'page' => $request->get('page', 1),
            'limit' => 10,
            'search' => $request->get('search'),
        ];

        $response = $this->service->getMerchants(array_filter($params));
        $payload = $response['payload'] ?? [];
        
        $merchants = new \Illuminate\Pagination\LengthAwarePaginator(
            $payload['docs'] ?? [],
            $payload['totalDocs'] ?? 0,
            $payload['limit'] ?? 10,
            $payload['page'] ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.merchants.index', compact('merchants'));
    }

    /**
     * Display the specified merchant.
     */
    public function show(Request $request, $id)
    {
        $merchantResponse = $this->service->getMerchant($id);
        $merchant = $merchantResponse['payload'] ?? null;

        if (!$merchant) {
            abort(404);
        }

        $reviewsParams = [
            'page' => $request->get('page', 1),
            'limit' => 5,
        ];

        $reviewsResponse = $this->service->getMerchantRatings($id, $reviewsParams);
        $payload = $reviewsResponse['payload'] ?? [];
        
        $reviews = new \Illuminate\Pagination\LengthAwarePaginator(
            $payload['docs'] ?? [],
            $payload['totalDocs'] ?? 0,
            $payload['limit'] ?? 5,
            $payload['page'] ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Fetch analytics
        $analyticsResponse = $this->service->getAnalytics($id);
        $analytics = $analyticsResponse['payload'] ?? null;

        return view('admin.merchants.show', compact('merchant', 'reviews', 'analytics'));
    }

    /**
     * Approve a merchant's CAC verification.
     */
    public function approve($id)
    {
        $response = $this->service->updateMerchantStatus($id, true, 'approved');

        if ($response && ($response['status'] ?? '') === 'OK') {
            return redirect()->back()->with('success', 'Merchant CAC verified and account activated.');
        }

        return redirect()->back()->with('error', $response['msg'] ?? 'Failed to approve merchant.');
    }

    /**
     * Reject a merchant's CAC verification.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $response = $this->service->updateMerchantStatus($id, false, 'rejected', $request->reason);

        if ($response && ($response['status'] ?? '') === 'OK') {
            return redirect()->back()->with('success', 'Merchant verification rejected.');
        }

        return redirect()->back()->with('error', $response['msg'] ?? 'Failed to reject merchant.');
    }
}
