<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Validation\FormValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function profile()
    {
        $id = Auth::guard('customer')->id();
        $customer = Customer::find($id);
        return view('customers.profile', ['c' => $customer]);
    }

    public function doProfile(Request $request)
    {
        $id = Auth::guard('customer')->id();
        $c = Customer::find($id);

        $validate = [];
        if ($request->filled('name') && ! empty($request->name) && $c->name !== $request->name) {
            $validate[] = 'name';
        } else if ($request->filled('password') && ! empty($request->password) && ! Hash::check($request->password, $c->password)) {
            $validate[] = 'password';
            $validate[] = 'confirmPassword';
        }

        if (! empty($validate)) {
            FormValidation::validate($request, $validate);

            if (in_array('name', $validate)) {
                $c->name = $request->name;
            }

            if (in_array('password', $validate)) {
                $c->password = Hash::make($request->password);
            }

            $c->save();
            return redirect('/customers/profile')->with('msg', 'updateSuccess');
        } else {
            return redirect('/customers/profile')->with('msg', 'updateNoChanges');
        }
    }

    public function register()
    {
        return view('customers.register');
    }

    public function doRegister(Request $request)
    {
        FormValidation::validate($request, ['email', 'name', 'password', 'confirmPassword']);

        $this->cache($request);
        return redirect('/customers/register')->with('msg', 'registerSuccess');
    }

    public function verify(Request $request)
    {
        $token = $request->query('token');

        if (Cache::has($token)) {

            $data = Cache::get($token);

            $c = new Customer();
            $c->email = $data['email'];
            $c->name = $data['name'];
            $c->password = $data['password'];
            $c->save();

            Cache::forget($token);
            Cache::forget($data['email']);

            return view('customers.verify', ['msg' => 'verifySuccess']);
        }

        return view('customers.verify', ['msg' => 'verifyFail']);
    }

    /**
     * Display a listing of the resource.
     * GET /model
     */
    public function index(Request $request)
    {
        $query = $request->input('query');

        if ($query) {
            $customers = Customer::where('id', 'LIKE', '%' . $query . '%')
                ->orWhere('name', 'LIKE', '%' . $query . '%')
                ->orWhere('email', 'LIKE', '%' . $query . '%')
                ->orWhere('point', 'LIKE', '%' . $query . '%')
                ->paginate(8);
        } else {
            $customers = Customer::paginate(8);
        }

        return view('customers.index', compact('customers', 'query'));
    }

    /**
     * Show the form for creating a new resource.
     * GET /model/create
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     * POST /model
     */
    public function store(Request $request)
    {
        FormValidation::validate($request, ['email', 'name', 'password', 'confirmPassword']);

        if (Cache::has($request->email)) {
            return redirect('/customers/create')->with('msg', 'addExist');

        } else {
            $this->cache($request);
            return redirect('/customers/create')->with('msg', 'addSuccess');
        }
    }

    /**
     * Display the specified resource.
     * GET /model/{id}
     */
    public function show(Customer $customer)
    {
        return view('customers.show', ['c' => $customer]);
    }

    /**
     * Show the form for editing the specified resource.
     * GET /model/{id}/edit
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', ['c' => $customer]);
    }

    /**
     * Update the specified resource in storage.
     * PUT /model/{id}
     */
    public function update(Request $request, Customer $customer)
    {
        FormValidation::validate($request, ['name', 'password', 'confirmPassword']);

        $c = $customer;

        if ($c->name !== $request->name) {
            $c->name = $request->name;
        }
        if (! Hash::check($request->password, $c->password)) {
            $c->password = Hash::make($request->password);
        }

        if ($c->isDirty()) {
            $c->save();
            return redirect('/customers/' . $c->id . '/edit')->with(['msg' => 'updateSuccess', 'id' => $c->id]);
        } else {
            return redirect('/customers/' . $c->id . '/edit')->with(['msg' => 'updateNoChanges', 'id' => $c->id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /model/{id}
     */
    public function destroy(Customer $customer)
    {
        $c = $customer;
        $c->delete();

        return redirect('/customers')->with('msg', 'deleteSuccess');
    }

    private function cache(Request $request)
    {
        $token = Str::random(60);
        $email = $request->email;
        $name = $request->name;
        $password = Hash::make($request->password);

        // Send email
        MailController::verifyEmail($email, $token);

        $customerData = [
            'email' => $email,
            'name' => $name,
            'password' => $password,
        ];

        Cache::put($token, $customerData, now()->addMinutes(10));   // For customer
        Cache::put($email, $token, now()->addMinutes(10));          // For staff
    }
}