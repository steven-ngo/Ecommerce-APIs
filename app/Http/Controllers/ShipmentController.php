<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class ShipmentController extends Controller
{
    // Create a new shipment
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'order_detail_id' => 'required|exists:order_details,id',
            'ship_via' => 'required|string|max:255',
            'is_shipped' => 'required|boolean',
            'status' => 'required|string|max:255',
        ]);

        // Create new shipment
        DB::transaction(
            function () use ($validatedData) {
                DB::table('shipments')->insertGetId([
                    'order_detail_id'   => $validatedData['order_detail_id'],
                    'ship_via'          => $validatedData['ship_via'],
                    'is_shipped'        => $validatedData['is_shipped'],
                    'status'            => $validatedData['status'],
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            },
            2
        );

        // Return response
        return response()->json(['message' => 'Shipment created successfully'], 201);
    }

    // Retrieve all shipments
    public function index()
    {
        // Retrieve all shipments
        $shipments = DB::table('shipments')->get()->toArray();

        // Return response
        return response()->json(['data' => $shipments], 200);
    }

    // Retrieve a specific shipment by ID
    public function show($shipmentId)
    {
        $shipment = DB::table('shipments')->find($shipmentId);

        if (!$shipment) {
            return response()->json(['message' => 'the id is not matched'], 404);
        }

        $shipment = DB::table('shipments')
            ->where('id', '=', $shipmentId)
            ->get()->toArray();

        // Return response
        return response()->json(['data' => $shipment], 200);
    }

    // Update shipment status of an existing shipment
    public function update(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'id' => 'required|exists:shipments,id',
            'status' => 'required|string'
        ]);

        $shipment = DB::table('shipments')->find($validatedData['id']);

        if (!$shipment) {
            return response()->json(['message' => 'the id is not matched'], 404);
        }

        DB::table('shipments')
            ->where('id', '=', $validatedData['id'])
            ->update(['status' => $validatedData['status']]);

        // Return response
        return response()->json(['message' => "shipment's status updated successfully"], 200);
    }
}
