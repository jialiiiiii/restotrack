<?php

namespace App\Http\Controllers;

use App\Events\ChangesNotification;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderMeal;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    protected $reservePoints = 250;

    public function assign(Request $request)
    {
        $rid = $request->input("id");
        $tid = $request->input("table");

        $reservation = Reservation::find($rid);
        $order = $reservation->order;

        // Change order status, table id
        $order->status = 'pending';
        $order->table_id = $tid;
        $order->save();

        // Inform order page
        event(new ChangesNotification('orders'));
        // Inform order manage page
        event(new ChangesNotification('orders.manage'));

        // Change table status
        Table::find($tid)->update(['status' => 'occupied']);

        // Inform track page
        event(new ChangesNotification('tables'));

        // Remove reservation
        $reservation->delete();

        return response()->json(['message' => 'success']);
    }

    public function session(Request $request)
    {
        $request->session()->put("cartReserve", Auth::guard('customer')->user()->id);

        return redirect('/menu');
    }

    public function view(Request $request)
    {
        $uid = auth()->guard('customer')->user()->id;

        $reservations = Reservation::whereHas('order', function ($query) use ($uid) {
            $query->where('user_id', $uid);
        })->get();

        return view("reservations.view", compact('reservations'));
    }

    /**
     * Display a listing of the resource.
     * GET /model
     */
    public function index(Request $request)
    {
        $results = Reservation::orderBy('created_at', 'desc');

        $type = $request->input('type') ?? 'all';
        if ($type == 'today') {
            $results->whereDate('datetime', today());
        } else if ($type == 'past') {
            $results->whereDate('datetime', '<', now());
        } else if ($type == 'future') {
            $results->whereDate('datetime', '>', now());
        }

        $query = $request->input('query');
        if ($query) {
            $results->where(function ($queryBuilder) use ($query) {
                // Check if the query is date
                try {
                    $date = Carbon::createFromFormat('d/m/Y', $query);
                    $formattedDate = $date->toDateString();
                    $queryBuilder->orWhereDate('datetime', '=', $formattedDate);
                } catch (\Exception $e) {}

                // Check if the query is time
                try {
                    $time = Carbon::createFromFormat('h:ia', $query);
                    $formattedTime = $time->format('H:i');
                    $queryBuilder->orWhereTime('datetime', '=', $formattedTime);
                } catch (\Exception $e) {}

                // Check if the query is an integer
                if (ctype_digit($query)) {
                    $queryBuilder->orWhere('pax', '=', $query);
                }
            });
        }

        $reservations = $results->orderBy('created_at', 'desc')->get();

        // Get customer name
        foreach ($reservations as $r) {
            $uid = $r->order->user_id;
            $c = Customer::find($uid);

            $r->order->user_name = $c->name;
        }

        return view("reservations.index", compact('reservations', 'type', 'query'));
    }

    /**
     * Show the form for creating a new resource.
     * GET /model/create
     */
    public function create()
    {
        $cartReserve = false;
        $results = [];

        if (Auth::guard('customer')->check()) {
            $user = Auth::guard('customer')->user();

            if ($user->point >= $this->reservePoints) {
                $message = 'reserve';
                $points = 0;
                $cartReserve = true;

                // Get carts
                $carts = Cart::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

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

            } else {
                $message = 'pointsRequired';
                $points = $this->reservePoints - $user->point;
            }
        } else {
            $message = 'loginRequired';
            $points = $this->reservePoints;
        }

        return view("reservations.create", compact("message", "points", "cartReserve", "results"));
    }

    /**
     * Store a newly created resource in storage.
     * POST /model
     */
    public function store(Request $request)
    {
        // Get carts
        $uid = Auth::guard('customer')->user()->id;
        $carts = Cart::where('user_id', $uid)->get();
        if (empty($carts)) {
            return redirect('/reservations/create');
        }

        // Create order
        $o = new Order();
        $o->user_id = $uid;
        $o->table_id = 100;
        $o->type = 'dine-in';
        $o->status = 'reserved';
        $o->save();

        // Create order meal
        $oid = $o->id;
        foreach ($carts as $cart) {
            $meal = $cart->meal;
            $mid = $meal->id;
            $quantity = $cart->quantity;
            $price = $meal->price * $quantity;

            if ($meal->sales > 0) {
                $price = ($price * (100 - $meal->sales)) / 100;
            }

            $om = new OrderMeal();
            $om->order_id = $oid;
            $om->meal_id = $mid;
            $om->price = number_format($price, 2);
            $om->quantity = $quantity;
            $om->save();

            // Delete cart item
            $cart->delete();
        }

        $pax = $request->input("pax");
        $date = $request->input("date");
        $time = $request->input("time");
        $datetime = Carbon::parse("$date $time");

        // Create reservation
        $r = new Reservation([
            'order_id' => $oid,
            'pax' => $pax,
            'datetime' => $datetime,
        ]);
        $r->save();

        // Remove session
        $request->session()->forget("cartReserve");

        // Inform reservation manage page
        event(new ChangesNotification('reservations.manage'));

        return redirect('/reservations/view')->with('msg', 'reservationMade');
    }

    /**
     * Display the specified resource.
     * GET /model/{id}
     */
    public function show(Reservation $reservation)
    {

    }

    /**
     * Show the form for editing the specified resource.
     * GET /model/{id}/edit
     */
    public function edit(Reservation $reservation)
    {

    }

    /**
     * Update the specified resource in storage.
     * PUT /model/{id}
     */
    public function update(Request $request, Reservation $reservation)
    {
        $pax = $request->input('pax');
        $date = $request->input('date');
        $time = $request->input('time');
        $datetime = Carbon::parse("$date $time");

        $reservation->pax = $pax;
        $reservation->datetime = $datetime;
        $reservation->save();

        // Inform reservation manage page
        event(new ChangesNotification('reservations.manage'));

        return response()->json(['message' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /model/{id}
     */
    public function destroy(Reservation $reservation)
    {
        // Delete order meals
        $reservation->order->orderMeals()->delete();

        // Delete reservation
        $reservation->delete();

        // Delete order
        $reservation->order->delete();

        // Inform reservation manage page
        event(new ChangesNotification('reservations.manage'));

        return response()->json(['message' => 'success']);
    }
}
