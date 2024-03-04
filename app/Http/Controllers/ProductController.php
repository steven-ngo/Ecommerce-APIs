<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class ProductController extends Controller
{
    // Create a new product
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer',
            'unit_price' => 'required|numeric',
        ]);

        // Create new product
        DB::transaction(
            function () use ($validatedData) {
                $id = DB::table('products')->insertGetId([
                    'name'          => $validatedData['name'],
                    'description'   => $validatedData['description'],
                    'category_id'   => $validatedData['category_id'],
                    'unit_price'    => $validatedData['unit_price'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                DB::table('inventories')->insert([
                    'product_id'    => $id,
                    'warehouse_id'  => $validatedData['warehouse_id'],
                    'quantity'      => $validatedData['quantity'],
                    'active'        => 1,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            },
            2
        );

        // Return response
        return response()->json(['message' => 'Product created successfully'], 201);
    }

    // Retrieve all items
    public function index()
    {
        // Retrieve all items
        $products = DB::table('products')
            ->select('inventories.id', 'products.name AS ProductName', 'products.description AS ProductDescription', 'categories.name AS categoryName', 'unit_price', 'warehouses.name AS warehouseName', 'quantity', 'active')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('inventories', 'inventories.product_id', '=', 'products.id')
            ->join('warehouses', 'warehouses.id', '=', 'inventories.warehouse_id')
            ->get()->toArray();

        // Return response
        return response()->json(['data' => $products], 200);
    }

    // Retrieve a specific item by ID
    public function show($productId)
    {
        $product = DB::table('products')->find($productId);

        if (!$product) {
            return response()->json(['message' => 'the id is not matched'], 404);
        }

        $products = DB::table('products')
            ->select('inventories.id', 'products.name AS ProductName', 'products.description AS ProductDescription', 'categories.name AS categoryName', 'unit_price', 'warehouses.name AS warehouseName', 'quantity', 'active')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('inventories', 'inventories.product_id', '=', 'products.id')
            ->join('warehouses', 'warehouses.id', '=', 'inventories.warehouse_id')
            ->where('products.id', '=', $productId)
            ->get()->toArray();

        // Return response
        return response()->json(['data' => $products], 200);
    }

    // Update quantity of an existing product
    public function update(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'id' => 'required|exists:inventories,id',
            'quantity' => 'required|integer'
        ]);

        $inventory = DB::table('inventories')->find($validatedData['id']);

        if (!$inventory) {
            return response()->json(['message' => 'the id is not matched'], 404);
        }

        $products = DB::table('inventories')
            ->where('id', '=', $validatedData['id'])
            ->update(['quantity' => $validatedData['quantity']]);

        // Return response
        return response()->json(['message' => "Product's quantity updated successfully"], 200);
    }

    // Delete an item by ID
    public function destroy($productId)
    {
        $product = DB::table('products')->find($productId);

        if (!$product) {
            return response()->json(['message' => 'the id is not matched'], 404);
        }

        // Create new item
        DB::transaction(
            function () use ($productId) {
                DB::table('inventories')->where('product_id', '=', $productId)->delete();
                DB::table('products')->where('id', '=', $productId)->delete();
            },
            1
        );

        // Return response
        return response()->json(['message' => 'product has been deleted'], 200);
    }
}
