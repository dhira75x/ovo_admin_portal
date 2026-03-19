<?php

namespace App\Http\Controllers;

use App\Services\EcommerceService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $ecommerceService;

    public function __construct(EcommerceService $ecommerceService)
    {
        $this->ecommerceService = $ecommerceService;
    }

    public function index()
    {
        $analytics = $this->ecommerceService->getAnalytics();
        \Log::info("Dashboard index analytics response: " . json_encode($analytics));
        return view('admin.dashboard', compact('analytics')); 
    }

    public function notificationsCount()
    {
        $userId = session('ecommerce_user_id');
        $sessionKeys = array_keys(session()->all());
        \Log::info("Fetch notifications count. User: " . ($userId ?? 'MISSING') . ". Session keys: " . implode(', ', $sessionKeys));

        $response = $this->ecommerceService->getUnreadTicketMessagesCount();
        \Log::info("Unread count API response: " . json_encode($response));

        $count = $response['payload']['count'] ?? 0;
        
        return response()->json([
            'count' => $count
        ]);
    }
}
