<?php

namespace App\Http\Controllers;

use App\Jobs\RemoveCarts;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    function session(Request $request)
    {
        if ($request->filled("user")) {
            $request->session()->put("cartUser", $request->input("user"));
        }

        if ($request->filled("type")) {
            $request->session()->put("cartType", $request->input("type"));
        }

        return response()->json(['message' => 'success']);
    }

    /**
     * Display a listing of the resource.
     * GET /model
     */
    public function index(Request $request)
    {
        
    }

    /**
     * Show the form for creating a new resource.
     * GET /model/create
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     * POST /model
     */
    public function store(Request $request)
    {
        $mealId = $request->input('mealId');
        $userId = $request->session()->get("cartUser");
        $result = Cart::where('user_id', $userId)->where('meal_id', $mealId)->first();

        if ($result !== null){
            $c = $result;
        }
        else {
            $c = new Cart();
        }
        $quantity = $c->quantity ?? 0;

        $c->user_id = $userId;
        $c->type = $request->session()->get("cartType");
        $c->meal_id = $mealId;
        $c->quantity = $quantity + 1;
        $c->save();

        // Clear carts after 30 minutes
        RemoveCarts::dispatch($userId)->delay(now()->addMinutes(30));

        return response()->json(['message' => 'success']);
    }

    /**
     * Display the specified resource.
     * GET /model/{id}
     */
    public function show(Cart $cart)
    {

    }

    /**
     * Show the form for editing the specified resource.
     * GET /model/{id}/edit
     */
    public function edit(Cart $cart)
    {

    }

    /**
     * Update the specified resource in storage.
     * PUT /model/{id}
     */
    public function update(Request $request, Cart $cart)
    {

    }

    /**
     * Remove the specified resource from storage.
     * DELETE /model/{id}
     */
    public function destroy(Cart $cart)
    {

    }
}
