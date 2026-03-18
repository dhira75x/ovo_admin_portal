<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    /**
     * Display a listing of merchants with their ratings.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'merchant')
            ->withCount('reviewsReceived')
            ->withAvg('reviewsReceived', 'rating');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $merchants = $query->latest()->paginate(10);

        return view('admin.merchants.index', compact('merchants'));
    }

    /**
     * Display the specified merchant with reviews.
     */
    public function show($id)
    {
        $merchant = User::where('role', 'merchant')
            ->withCount('reviewsReceived')
            ->withAvg('reviewsReceived', 'rating')
            ->findOrFail($id);

        $reviews = $merchant->reviewsReceived()
            ->with('customer')
            ->latest()
            ->paginate(10);

        return view('admin.merchants.show', compact('merchant', 'reviews'));
    }
}
