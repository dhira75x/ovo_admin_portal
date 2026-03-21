<?php

namespace App\Http\Controllers;

use App\Services\EcommerceService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    protected $ecommerceService;

    public function __construct(EcommerceService $ecommerceService)
    {
        $this->ecommerceService = $ecommerceService;
    }

    public function index(Request $request)
    {
        $withdrawals = $this->ecommerceService->getWithdrawals($request->all());
        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function approve($id)
    {
        $response = $this->ecommerceService->approveWithdrawal($id);

        if ($response['status'] === 'OK') {
            return redirect()->back()->with('success', 'Withdrawal approved successfully');
        }

        return redirect()->back()->with('error', $response['msg'] ?? 'Failed to approve withdrawal');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:255'
        ]);

        $response = $this->ecommerceService->rejectWithdrawal($id, $request->comment);

        if ($response['status'] === 'OK') {
            return redirect()->back()->with('success', 'Withdrawal rejected and funds refunded');
        }

        return redirect()->back()->with('error', $response['msg'] ?? 'Failed to reject withdrawal');
    }
}
