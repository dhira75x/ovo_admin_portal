<?php

namespace App\Http\Controllers;

use App\Services\EcommerceService;
use Illuminate\Http\Request;

class OrderController extends Controller
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
            'search' => $request->get('search'),
        ];

        $response = $this->service->getOrders(array_filter($params));
        $payload = $response['payload'] ?? [];
        
        $orders = new \Illuminate\Pagination\LengthAwarePaginator(
            $payload['docs'] ?? [],
            $payload['totalDocs'] ?? 0,
            $payload['limit'] ?? 10,
            $payload['page'] ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $response = $this->service->getOrder($id);
        $order = $response['payload'] ?? null;

        if (!$order) {
            abort(404);
        }

        return view('admin.orders.show', compact('order'));
    }
}
