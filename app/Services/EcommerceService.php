<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EcommerceService
{
    protected $baseUrl;
    protected $adminEmail;
    protected $adminPassword;
    protected $token = null;

    public function __construct()
    {
        $this->baseUrl = config('services.ecommerce.url', env('ECOMMERCE_BACKEND_URL'));
        $this->adminEmail = config('services.ecommerce.email', env('ECOMMERCE_BACKEND_ADMIN_EMAIL'));
        $this->adminPassword = config('services.ecommerce.password', env('ECOMMERCE_BACKEND_ADMIN_PASSWORD'));
    }

    public function loginAdmin($email, $password)
    {
        try {
            $response = Http::post("{$this->baseUrl}/auth/login/admins", [
                'email' => $email,
                'password' => $password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->token = $data['key'] ?? null;
                session(['ecommerce_token' => $this->token]);
                return $data;
            }

            Log::error('Ecommerce login failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Ecommerce login exception: ' . $e->getMessage());
        }

        return null;
    }

    protected function request()
    {
        $token = $this->token ?? session('ecommerce_token');

        if (!$token) {
            // If no token in session, we might need a system-level login or handle it in controllers
            return Http::withOptions(['verify' => false]);
        }

        return Http::withToken($token)->withOptions(['verify' => false]);
    }

    public function getAnalytics($merchantId = null)
    {
        $endpoint = $merchantId ? "/analytics/merchant/{$merchantId}" : "/analytics/overall"; // Need to verify if overall exists
        $response = $this->request()->get("{$this->baseUrl}{$endpoint}");
        return $response->json();
    }

    public function getOrders($params = [])
    {
        $response = $this->request()->get("{$this->baseUrl}/orders", $params);
        return $response->json();
    }

    public function getOrder($id)
    {
        $response = $this->request()->get("{$this->baseUrl}/orders/{$id}");
        return $response->json();
    }

    public function getMerchants($params = [])
    {
        $response = $this->request()->get("{$this->baseUrl}/merchants", $params);
        return $response->json();
    }

    public function getMerchant($id)
    {
        $response = $this->request()->get("{$this->baseUrl}/merchants/{$id}");
        return $response->json();
    }

    public function getTickets($params = [])
    {
        $response = $this->request()->get("{$this->baseUrl}/tickets", $params);
        return $response->json();
    }

    public function getTicket($id)
    {
        $response = $this->request()->get("{$this->baseUrl}/tickets/{$id}");
        return $response->json();
    }

    public function getTicketMessages($id)
    {
        $response = $this->request()->get("{$this->baseUrl}/tickets/{$id}/messages");
        return $response->json();
    }

    public function getKycSubmissions($params = [])
    {
        $response = $this->request()->get("{$this->baseUrl}/verification/documents", $params);
        return $response->json();
    }

    public function updateKycStatus($id, $status, $rejectionReason = null)
    {
        $data = ['status' => $status];
        if ($rejectionReason) {
            $data['rejectionReason'] = $rejectionReason;
        }

        $response = $this->request()->patch("{$this->baseUrl}/verification/status/{$id}", $data);
        return $response->json();
    }

    public function updateMerchantStatus($id, $isVerified, $cacStatus = null, $cacRejectionReason = null)
    {
        $data = ['isVerified' => $isVerified];
        if ($cacStatus) {
            $data['cacStatus'] = $cacStatus;
        }
        if ($cacRejectionReason) {
            $data['cacRejectionReason'] = $cacRejectionReason;
        }

        $response = $this->request()->patch("{$this->baseUrl}/merchants/status/{$id}", $data);
        return $response->json();
    }

    public function replyToTicket($ticketId, $text)
    {
        $response = $this->request()->post("{$this->baseUrl}/tickets/messages", [
            'ticketId' => $ticketId,
            'text' => $text,
        ]);
        return $response->json();
    }

    public function updateTicketStatus($ticketId, $params)
    {
        $response = $this->request()->patch("{$this->baseUrl}/tickets/status/{$ticketId}", $params);
        return $response->json();
    }

    public function getMerchantRatings($merchantId, $params = [])
    {
        $params['merchantId'] = $merchantId;
        $response = $this->request()->get("{$this->baseUrl}/ratings/merchants", $params);
        return $response->json();
    }

    public function getUnreadTicketMessagesCount()
    {
        try {
            $response = $this->request()->get("{$this->baseUrl}/tickets/unread-count");
            if (!$response->successful()) {
                Log::warning('Failed to fetch unread count: ' . $response->status());
                return ['payload' => ['count' => 0]];
            }
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error fetching unread count: ' . $e->getMessage());
            return ['payload' => ['count' => 0]];
        }
    }

    public function markAllTicketMessagesAsViewed($ticketId, $userId)
    {
        try {
            $response = $this->request()->put("{$this->baseUrl}/tickets/status/viewed-all", [
                'ticketId' => $ticketId,
                'userId' => $userId
            ]);
            
            if (!$response->successful()) {
                Log::error("Failed to mark messages as viewed for ticket {$ticketId}: " . $response->body());
            }
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error("Exception marking messages as viewed for ticket {$ticketId}: " . $e->getMessage());
            return null;
        }
    }
}
