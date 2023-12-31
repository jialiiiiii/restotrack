<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Meal;
use App\Models\Table;
use App\Validation\FormValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class MealController extends Controller
{
    public function menu(Request $request)
    {
        $addToCart = false;
        $cartQuantity = 0;

        // Ordering
        if ($request->filled('table') || $request->session()->has("cartTable")) {

            // Table
            if (is_numeric($request->input('table'))) {
                $tid = $request->input('table');
                $tableResult = Table::where('id', $tid)->where('status', 'available')->where('seat', '>', 0)->exists();

            } else if (is_numeric($request->session()->get("cartTable"))) {
                $tid = $request->session()->get("cartTable");
                $tableResult = Table::where('id', $tid)->where('seat', '>', 0)->exists();

            } elseif ($request->input('table') === "ta" || $request->session()->get("cartTable") === "ta") {
                $tid = "ta";
                $tableResult = true;
            } else {
                $tableResult = false;
            }

            if ($tableResult) {
                $addToCart = true;
                $request->session()->put("cartTable", $tid);

                if ($tid === "ta") {
                    $request->session()->put("cartType", "takeaway");
                }
            } else {
                $request->session()->forget(["cartTable", "cartType"]);
            }

            // Cart
            if ($request->session()->has('cartUserId')) {
                $uid = $request->session()->get('cartUserId');
                $cartResult = Cart::where('user_id', $uid)->sum('quantity');
                $cartQuantity = $cartResult;
            }
            // Make reservation (customers only)
        } else if ($request->session()->has("cartReserve")) {
            $addToCart = true;

            $uid = Auth::guard('customer')->user()->id;
            $cartResult = Cart::where('user_id', $uid)->sum('quantity');
            $cartQuantity = $cartResult;
        }

        $resetPage = false;

        $meals = Meal::notDeleted()->isAvailable();

        $categories = Meal::distinct('category')->pluck('category')->toArray();

        $category = $request->input('category');
        $sort = $request->input('sort');
        $query = $request->input('query');

        if ($category && $category !== 'all') {
            $meals = $meals->where('category', '=', $category);
            $resetPage = true;
        }

        if ($sort == 'old-new') {
            $meals = $meals->orderBy('id', 'asc');
        } elseif ($sort == 'new-old') {
            $meals = $meals->orderBy('id', 'desc');
        } elseif ($sort == 'low-high') {
            $meals = $meals->orderByRaw('(price * (100 - sales)) / 100 ASC');
        } elseif ($sort == 'high-low') {
            $meals = $meals->orderByRaw('(price * (100 - sales)) / 100 DESC');
        }

        if ($query) {
            $meals = $meals->where(function ($queryBuilder) use ($query) {
                $queryBuilder->orWhere('name', 'LIKE', '%' . $query . '%')
                    ->orWhere('category', 'LIKE', '%' . $query . '%')
                    ->orWhere(function ($queryBuilder) use ($query) {
                        $queryBuilder->where('price', '=', $query)
                            ->orWhereRaw('(price * (100 - sales) / 100) = ?', [$query]);
                    });
            });
            $resetPage = true;
        }

        $meals = $resetPage ? $meals->paginate(20, ['*'], 1) : $meals->paginate(20);

        return view('meals.menu', compact('meals', 'categories', 'query', 'addToCart', 'cartQuantity'));
    }

    public function toggleAvailability($id)
    {
        $m = Meal::find($id);

        $m->available = ! $m->available;
        $m->save();

        return redirect('/meals')->with(['msg' => 'toggleSuccess', 'id' => $m->id, 'result' => ($m->available ? 'available' : 'not available')]);
    }

    /**
     * Display a listing of the resource.
     * GET /model
     */
    public function index(Request $request)
    {
        $query = $request->input('query');

        if ($query) {
            $meals = Meal::notDeleted()
                ->where('id', 'LIKE', '%' . $query . '%')
                ->orWhere('name', 'LIKE', '%' . $query . '%')
                ->orWhere('category', 'LIKE', '%' . $query . '%')
                ->orWhere('price', '=', $query)
                ->orWhereRaw('(price * (100 - sales) / 100) = ?', [$query])
                ->paginate(8);
        } else {
            $meals = Meal::notDeleted()->paginate(8);
        }

        return view('meals.index', compact('meals', 'query'));
    }

    /**
     * Show the form for creating a new resource.
     * GET /model/create
     */
    public function create()
    {
        $categories = Meal::distinct('category')->pluck('category')->toArray();

        return view('meals.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     * POST /model
     */
    public function store(Request $request)
    {
        FormValidation::validate($request, ['mealImage', 'mealName', 'mealCategory', 'mealDescription', 'mealPrice', 'mealSales']);

        $m = new Meal();
        $m->name = $request->mealName;
        $m->description = preg_replace('/[\r\n\t]+/', ' ', $request->mealDescription);
        $m->category = ucwords($request->mealCategory);
        $m->price = $request->mealPrice;
        $m->sales = $request->mealSales;
        $m->save();

        $image = $request->file('mealImage');
        if ($image) {
            Image::make($image)->fit(300)->save(public_path("/img/meals/$m->id.png"));
        }

        return redirect('/meals/create')->with(['msg' => 'addSuccess', 'id' => $m->id]);
    }

    /**
     * Display the specified resource.
     * GET /model/{id}
     */
    public function show(Meal $meal)
    {
        return view('meals.show', ['m' => $meal]);
    }

    /**
     * Show the form for editing the specified resource.
     * GET /model/{id}/edit
     */
    public function edit(Meal $meal)
    {
        $categories = Meal::distinct('category')->pluck('category')->toArray();

        return view('meals.edit', ['m' => $meal, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     * PUT /model/{id}
     */
    public function update(Request $request, Meal $meal)
    {
        $validate = ['mealName', 'mealCategory', 'mealDescription', 'mealPrice', 'mealSales'];
        if ($request->hasFile('mealImage')) {
            $validate[] = 'mealImage';
        }
        FormValidation::validate($request, $validate);

        $m = $meal;

        if ($m->name !== $request->mealName) {
            $m->name = $request->mealName;
        }
        if ($m->category !== $request->mealCategory) {
            $m->category = $request->mealCategory;
        }
        if ($m->description !== $request->mealDescription) {
            $m->description = $request->mealDescription;
        }
        if ($m->price !== $request->mealPrice) {
            $m->price = $request->mealPrice;
        }
        if ($m->sales !== $request->mealSales) {
            $m->sales = $request->mealSales;
        }

        $image = $request->file('mealImage');
        if ($image) {
            Image::make($image)->fit(300)->save(public_path("/img/meals/$m->id.png"));
        }

        if ($m->isDirty() || $image) {
            $m->save();
            return redirect('/meals/' . $m->id . '/edit')->with(['msg' => 'updateSuccess', 'id' => $m->id]);
        } else {
            return redirect('/meals/' . $m->id . '/edit')->with(['msg' => 'updateNoChanges', 'id' => $m->id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /model/{id}
     */
    public function destroy(Meal $meal)
    {
        $m = $meal;
        $m->deleted = true;
        $m->save();

        return redirect('/meals')->with('msg', 'deleteSuccess');
    }
}