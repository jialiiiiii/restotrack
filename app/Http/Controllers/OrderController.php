<?php

namespace App\Http\Controllers;

use App\Events\ChangesNotification;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderMeal;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function view(Request $request)
    {
        $uid = auth()->guard('customer')->check() ? auth()->guard('customer')->user()->id : $request->session()->getId();

        $orders = Order::with('orderMeals')->where('user_id', $uid)->where('status', '!=', 'reserved')
            ->orderByDefault()->orderBy('created_at', 'desc')->get();

        return view("orders.view", compact('orders'));
    }

    /**
     * Display a listing of the resource.
     * GET /model
     */
    public function index(Request $request)
    {
        $results = Order::with('orderMeals');

        $status = $request->input('status') ?? 'all';
        if ($status !== 'all') {
            $results->where('status', $status);
        }

        $query = $request->input('query');
        if ($query) {
            $results->whereHas('orderMeals', function ($queryBuilder) use ($query) {
                $queryBuilder->where('price', 'LIKE', '%' . $query . '%')
                    ->orWhere('quantity', 'LIKE', '%' . $query . '%')
                    ->orWhereHas('meal', function ($subQueryBuilder) use ($query) {
                        $subQueryBuilder->where('name', 'LIKE', '%' . $query . '%');
                    });
            });
        }

        $orders = $results->orderByDefault()->orderBy('created_at', 'desc')->get();

        return view("orders.index", compact('orders', 'status', 'query'));
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
        $uid = $request->userId;
        $tid = $request->session()->get("cartTable") === "ta" ? 101 : $request->session()->get("cartTable");
        $type = $request->session()->get("cartType");

        $carts = Cart::where('user_id', $uid)->get();

        if (empty($carts)) {
            return redirect('/carts');
        }

        // Create order
        $o = new Order();
        $o->user_id = $uid;
        $o->table_id = $tid;
        $o->type = $type;
        $o->status = 'pending';
        $o->save();

        $oid = $o->id;

        // Create order meal
        foreach ($carts as $cart) {
            $meal = $cart->meal;
            $mid = $meal->id;
            $quantity = $cart->quantity;
            $price = $meal->price * $quantity;

            if ($meal->sales > 0) {
                $price = ($price * (100 - $meal->sales)) / 100;
            }

            // Change meal sold
            Meal::where('id', $mid)->increment('sold', $quantity);

            $om = new OrderMeal();
            $om->order_id = $oid;
            $om->meal_id = $mid;
            $om->price = number_format($price, 2);
            $om->quantity = $quantity;
            $om->save();

            // Delete cart item
            $cart->delete();
        }

        if ($type == 'dine-in') {
            // Change table status
            Table::find($tid)->update(['status' => 'occupied']);

            // Inform track page
            event(new ChangesNotification('tables'));
        }

        // Inform order manage page
        event(new ChangesNotification('orders.manage'));

        return redirect('/orders/view')->with('msg', 'orderPlaced');
    }

    /**
     * Display the specified resource.
     * GET /model/{id}
     */
    public function show(Order $order)
    {

    }

    /**
     * Show the form for editing the specified resource.
     * GET /model/{id}/edit
     */
    public function edit(Order $order)
    {

    }

    /**
     * Update the specified resource in storage.
     * PUT /model/{id}
     */
    public function update(Request $request, Order $order)
    {
        $status = $request->input('status');

        $order->status = $status;
        $order->save();

        if ($status == 'paid' || $status == 'cancelled') {

            // Change table status
            if ($order->type == 'dine-in') {

                Table::find($order->table_id)->update(['status' => 'available']);

                // Inform track page
                event(new ChangesNotification('tables'));
            }

            if ($status === 'paid' && ($points = $this->handle($order)) > 0) {
                Session::flash('msg', 'pointsEarned');
                Session::flash('point', $points);
            }
        }

        if ($status != 'cancelled') {
            // Inform order page
            event(new ChangesNotification('orders'));
        }

        // Inform order manage page
        event(new ChangesNotification('orders.manage'));

        return response()->json(['message' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /model/{id}
     */
    public function destroy(Order $order)
    {

    }

    private function handle(Order $order)
    {
        $response = 0;
        $uid = $order->user_id;
        $customer = Customer::find($uid);

        if ($customer) {
            $orderMeals = $order->orderMeals;

            // Award points
            $points = $orderMeals->pluck('price')->sum();
            $points = intval(round($points, 0));

            $customer->point += $points;
            $customer->save();

            $response = $points;

            // Send receipt email
            MailController::receiptEmail($customer->email, $orderMeals, $points);
        }

        return $response;
    }

}
