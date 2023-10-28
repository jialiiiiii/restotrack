<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Validation\FormValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /model
     */
    public function index(Request $request)
    {
        $query = $request->input('query');

        if ($query) {
            $staff = Staff::staffOnly()
                ->where('id', 'LIKE', '%' . $query . '%')
                ->orWhere('name', 'LIKE', '%' . $query . '%')
                ->paginate(8);
        } else {
            $staff = Staff::staffOnly()->paginate(8);
        }

        return view('staff.index', compact('staff', 'query'));
    }

    /**
     * Show the form for creating a new resource.
     * GET /model/create
     */
    public function create()
    {
        return view('staff.create');
    }

    /**
     * Store a newly created resource in storage.
     * POST /model
     */
    public function store(Request $request)
    {
        FormValidation::validate($request, ['name', 'password', 'confirmPassword']);

        $s = new Staff();
        $s->name = $request->name;
        $s->password = Hash::make($request->password);
        $s->role = "staff";
        $s->save();

        return redirect('/staff/create')->with(['msg' => 'addSuccess', 'id' => $s->id]);
    }

    /**
     * Display the specified resource.
     * GET /model/{id}
     */
    public function show(Staff $staff)
    {
        return view('staff.show', ['s' => $staff]);
    }

    /**
     * Show the form for editing the specified resource.
     * GET /model/{id}/edit
     */
    public function edit(Staff $staff)
    {
        return view('staff.edit', ['s' => $staff]);
    }

    /**
     * Update the specified resource in storage.
     * PUT /model/{id}
     */
    public function update(Request $request, Staff $staff)
    {
        FormValidation::validate($request, ['name', 'password', 'confirmPassword']);

        $s = $staff;

        if ($s->name !== $request->name) {
            $s->name = $request->name;
        }
        if (!Hash::check($request->password, $s->password)) {
            $s->password = Hash::make($request->password);
        }

        if ($s->isDirty()) {
            $s->save();
            return redirect('/staff/' . $s->id . '/edit')->with(['msg' => 'updateSuccess', 'id' => $s->id]);
        } else {
            return redirect('/staff/' . $s->id . '/edit')->with(['msg' => 'updateNoChanges', 'id' => $s->id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /model/{id}
     */
    public function destroy(Staff $staff)
    {
        $s = $staff;
        $s->delete();

        return redirect('/staff')->with('msg', 'deleteSuccess');
    }
}