<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class OrderController extends Controller
{
    // Create a new order
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'PO' => 'required|string|max:255',
            'date' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'ship_address' => 'required|string|max:255',
            'grand_total' => 'required|numeric',
            'ship_via' => 'required|string|max:255',

            'order_details'                => 'required|array',
            'order_details.*.product_id'   => 'required|exists:products,id',
            'order_details.*.quantity'     => 'required|integer|max:255',
            'order_details.*.unit_price'   => 'required|numeric|max:255',
        ]);

        // Create new order
        DB::transaction(
            function () use ($validatedData) {
                $orderId = DB::table('orders')->insertGetId([
                    'PO'            => $validatedData['PO'],
                    'date'          => $validatedData['date'],
                    'name'          => $validatedData['name'],
                    'email'         => $validatedData['email'],
                    'phone'         => $validatedData['phone'],
                    'date'          => $validatedData['date'],
                    'ship_address'  => $validatedData['ship_address'],
                    'grand_total'   => $validatedData['grand_total'],
                    'ship_via'      => $validatedData['ship_via'],
                    'status'        => 'open',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                $orderDetails = [];

                foreach ($validatedData['order_details'] as $detail) {
                    $orderDetails[] = [
                        'order_id' => $orderId,
                        'product_id' => $detail['product_id'],
                        'quantity' => $detail['quantity'],
                        'unit_price' => $detail['unit_price'],
                        'status' => 'open',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }

                DB::table('order_details')->insert($orderDetails);
            },
            2
        );

        // Return response
        return response()->json(['message' => 'Order created successfully'], 201);
    }

    // Retrieve all orders
    public function index()
    {
        // Retrieve all orders
        $orders = DB::table('orders')
            ->select('orders.*', 'order_details.*', 'order_details.id as order_detail_id', 'orders.status as order_status', 'order_details.status as order_detail_status')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->get()->groupBy('order_id')->map(function ($order) {
                $orderDetails = $order->map(function ($item) {
                    return [
                        'order_detail_id' => $item->order_detail_id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'status' => $item->order_detail_status,
                    ];
                });

                return [
                    'order_id'      => $order[0]->order_id,
                    'PO'            => $order[0]->PO,
                    'date'          => $order[0]->date,
                    'name'          => $order[0]->name,
                    'email'         => $order[0]->email,
                    'phone'         => $order[0]->phone,
                    'ship_address'  => $order[0]->ship_address,
                    'grand_total'   => $order[0]->grand_total,
                    'ship_via'      => $order[0]->ship_via,
                    'status'        => $order[0]->order_status,
                    'order_details' => $orderDetails->toArray(),
                ];
            })->values()->toArray();

        // Return response
        return response()->json(['data' => $orders], 200);
    }

    // Retrieve a specific order by ID
    public function show($orderId)
    {
        $order = DB::table('orders')->find($orderId);

        if (!$order) {
            return response()->json(['message' => 'the id is not matched'], 404);
        }

        $order = DB::table('orders')
            ->select('orders.*', 'order_details.*', 'order_details.id as order_detail_id', 'orders.status as order_status', 'order_details.status as order_detail_status')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('orders.id', '=', $orderId)
            ->get()->groupBy('order_id')->map(function ($order) {
                $orderDetails = $order->map(function ($item) {
                    return [
                        'order_detail_id' => $item->order_detail_id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'status' => $item->order_detail_status,
                    ];
                });

                return [
                    'order_id'      => $order[0]->order_id,
                    'PO'            => $order[0]->PO,
                    'date'          => $order[0]->date,
                    'name'          => $order[0]->name,
                    'email'         => $order[0]->email,
                    'phone'         => $order[0]->phone,
                    'ship_address'  => $order[0]->ship_address,
                    'grand_total'   => $order[0]->grand_total,
                    'ship_via'      => $order[0]->ship_via,
                    'status'        => $order[0]->order_status,
                    'order_details' => $orderDetails->toArray(),
                ];
            })->values()->toArray();

        // Return response
        return response()->json(['data' => $order], 200);
    }

    // Update the order status
    public function update(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'id' => 'required|exists:orders,id',
            'status' => 'required|string'
        ]);

        $order = DB::table('orders')->find($validatedData['id']);

        if (!$order) {
            return response()->json(['message' => 'the id is not matched'], 404);
        }

        DB::table('orders')
            ->where('id', '=', $validatedData['id'])
            ->update(['status' => $validatedData['status']]);

        // Return response
        return response()->json(['message' => "Order's status updated successfully"], 200);
    }
}
