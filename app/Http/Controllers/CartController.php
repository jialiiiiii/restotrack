<?php

namespace App\Http\Controllers;

use App\Jobs\RemoveCarts;
use App\Models\Cart;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    function session(Request $request)
    {
        if ($request->filled("user") && $request->filled('userId')) {
            $newUser = $request->input("user");
            $newId = $request->input('userId');

            $oldUser = $request->session()->get("cartUser") ?? 'Guest';
            $oldId = $oldUser == 'Guest' ? $request->session()->getId() : $request->session()->get("cartUserId");

            if ($request->session()->has('cartUser') && $oldId != $newId) {
                Cart::where('user_id', $oldId)->update(['user_id' => $newId]);
            }

            $request->session()->put("cartUser", $newUser);
            $request->session()->put("cartUserId", $newId);
        }

        if ($request->filled("type")) {
            $newType = $request->input("type");

            if ($request->session()->has('cartUser')) {
                $uid = $request->session()->get("cartUserId");
                Cart::where('user_id', $uid)->update(['type' => $newType]);
            }

            $request->session()->put("cartType", $newType);
        }

        return response()->json(['message' => 'success']);
    }

    /**
     * Display a listing of the resource.
     * GET /model
     */
    public function index(Request $request)
    {
        $cartTable = false;
        $uid = '';
        $results = [];

        if ($request->session()->has('cartTable')) {
            $cartTable = true;
            $uid = $request->session()->get("cartUserId");

        } else if ($request->session()->has("cartReserve")) {
            // Customers only
            $uid = Auth::guard('customer')->user()->id;
        }

        if ($uid) {
            $carts = Cart::where('user_id', $uid)->orderBy('created_at', 'desc')->get();

            foreach ($carts as $cart) {
                $result = (object) [
                    'id' => $cart->id,
                    'type' => $cart->type,
                    'quantity' => $cart->quantity,
                    'meal_id' => $cart->meal->id,
                    'meal_name' => $cart->meal->name,
                    'meal_desc' => $cart->meal->description,
                    'meal_price' => $cart->meal->price,
                    'meal_sales' => $cart->meal->sales,
                ];

                $results[] = $result;
            }
        }

        return view('carts.index', compact('results', 'cartTable'));
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
        $type = $request->session()->get("cartType");

        // Make reservation
        if ($request->session()->has("cartReserve")) {
            $userId = Auth::guard('customer')->user()->id;
            $type = 'dine-in';
        }
        else {
            $userId = $request->session()->get("cartUserId");
        }

        $mealId = $request->input('mealId');

        $result = Cart::where('user_id', $userId)->where('meal_id', $mealId)->first();

        if ($result !== null) {
            $c = $result;
        } else {
            $c = new Cart();
        }
        $quantity = $c->quantity ?? 0;

        $c->user_id = $userId;
        $c->type = $type;
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
        $action = $request->input('action');
        $value = $request->input('value');

        if ($action == 'plus') {
            $cart->quantity++;
        } else if ($action == 'minus') {
            $cart->quantity--;
        } else {
            $cart->quantity = $value;
        }

        $cart->save();

        return response()->json(['message' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /model/{id}
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();
        return response()->json(['message' => 'success']);
    }
}
