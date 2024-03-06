<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShipmentController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(ProductController::class)->group(function () {
    // Retrieve all products
    Route::get('/products', 'index');

    // Retrieve a specific product's inventory by ID
    Route::get('/products/{productId}', 'show');

    // Create a new product
    Route::post('/products', 'store');

    // Update quantity of an existing item by ID
    Route::put('/products', 'update');

    // Delete an product by ID
    Route::delete('/products/{productId}', 'destroy');
});

Route::controller(OrderController::class)->group(function () {
    // Retrieve all inventories
    Route::get('/orders', 'index');

    // Retrieve a specific product's inventory by ID
    Route::get('/orders/{orderId}', 'show');

    // Create a new product
    Route::post('/orders', 'store');

    // Update quantity of an existing item by ID
    Route::put('/orders', 'update');
});

Route::controller(ShipmentController::class)->group(function () {
    // Retrieve all inventories
    Route::get('/shipments', 'index');

    // Retrieve a specific product's inventory by ID
    Route::get('/shipments/{shipmentId}', 'show');

    // Create a new product
    Route::post('/shipments', 'store');

    // Update quantity of an existing item by ID
    Route::put('/shipments', 'update');
});
