<?php

namespace App\Http\Controllers;

use App\Events\ChangesNotification;
use App\Models\Table;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function track(Request $request) {
        $tables = Table::all()->sortBy(function ($table) {
            return [$table->row, $table->col];
        });

        return view('tables.track', compact('tables'));
    }

    /**
     * Display a listing of the resource.
     * GET /model
     */
    public function index(Request $request)
    {
        $tables = Table::all()->sortBy(function ($table) {
            return [$table->row, $table->col];
        });

        return view('tables.index', compact('tables'));
    }

    public function arrange(Request $request)
    {
        $tables = Table::all()->sortBy(function ($table) {
            return [$table->row, $table->col];
        });

        return view('tables.arrange', compact('tables'));
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
        try {
            $tableData = $request->input('data');
    
            // Sort data
            $sorted = collect($tableData)->sortBy(function ($item) {
                // Get seat != 0 first, sort by row, then sort by col
                return [$item['seat'] == 0, $item['row'], $item['col']];
            })->values()->all();
    
            // Drop all data
            Table::query()->delete();
            DB::statement('ALTER TABLE tables AUTO_INCREMENT = 1');
    
            // Insert all data
            foreach ($sorted as $data) {
                Table::create($data);
            }
            
            // Inform track page
            event(new ChangesNotification('Table data changed'));

            return response()->json(['message' => 'success']);
            
        } catch (QueryException $e) {
            return response()->json(['error' => 'Error inserting data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     * GET /model/{id}
     */
    public function show(Table $table, QrCodeController $qrCodeController)
    {
        $data = $qrCodeController->show($table->id);
        return view('tables.show', ['t' => $table, 'qr' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     * GET /model/{id}/edit
     */
    public function edit(Table $table)
    {
        
    }

    /**
     * Update the specified resource in storage.
     * PUT /model/{id}
     */
    public function update(Request $request, Table $table)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /model/{id}
     */
    public function destroy(Table $table)
    {
        
    }
}
