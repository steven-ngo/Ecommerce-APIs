<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


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

Route::group([
    'namespace'  => 'api'
], function (Router $router) {

    // Retrieve all inventories
    $router->get('/inventories', [ProductController::class, 'index']);

    // Retrieve a specific product's inventory by ID
    $router->get('/inventories/{productId}', [ProductController::class, 'show']);

    // Create a new product
    $router->post('/products', [ProductController::class, 'store']);

    // Update quantity of an existing item by ID
    $router->put('/inventories', [ProductController::class, 'update']);

    // Delete an product by ID
    $router->delete('/products/{productId}', [ProductController::class, 'destroy']);
});
