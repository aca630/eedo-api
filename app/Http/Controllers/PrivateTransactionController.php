<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SlaughterPrivate; // ✅ use your existing model

class PrivateTransactionController extends Controller
{
    public function store(Request $request)
    {
        // ✅ Validate fields from your mobile payload
        $validated = $request->validate([
            'date' => 'required|date',
            'or_no' => 'required|string|max:255',
            'agency' => 'required|string|max:255',
            'owner' => 'required|string|max:255',
            'small_heads' => 'nullable|integer|min:0',
            'large_heads' => 'nullable|integer|min:0',
        ]);

        // ✅ Save to slaughter_privates table
        $record = SlaughterPrivate::create($validated);

        // ✅ Return JSON response
        return response()->json([
            'message' => 'Private transaction saved successfully!',
            'data' => $record
        ], 201);
    }

    public function latest()
{
    $record = \App\Models\SlaughterPrivate::orderByDesc('id')->first();

    if (!$record) {
        return response()->json([
            'success' => false,
            'message' => 'No records found.'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'id' => $record->id,
            'date' => $record->date,
            'or_no' => $record->or_no,
            'agency' => $record->agency,
            'owner' => $record->owner,
            'small_heads' => (int)$record->small_heads,
            'large_heads' => (int)$record->large_heads,
            'created_at' => $record->created_at,
            'updated_at' => $record->updated_at,
        ]
    ], 200);
}

public function computeLatest()
{
    // 🔹 1. Get latest record from slaughter_privates
    $record = \App\Models\SlaughterPrivate::orderByDesc('id')->first();

    if (!$record) {
        return response()->json([
            'success' => false,
            'message' => 'No records found.'
        ], 404);
    }

    // 🔹 2. Get livestock charges
    $charges = DB::table('livestock_charges')->get();

    $small = $charges->where('livestock_id', 1)->first();
    $large = $charges->where('livestock_id', 2)->first();

    if (!$small || !$large) {
        return response()->json([
            'success' => false,
            'message' => 'Livestock charges not found.'
        ], 404);
    }

    // 🔹 3. Compute totals
    $cf_total  = ($record->small_heads * $small->cf)  + ($record->large_heads * $large->cf);
    $sf_total  = ($record->small_heads * $small->sf)  + ($record->large_heads * $large->sf);
    $spf_total = ($record->small_heads * $small->spf) + ($record->large_heads * $large->spf);
    $pmf_total = ($record->small_heads * $small->pmf) + ($record->large_heads * $large->pmf);
    $total     = $cf_total + $sf_total + $spf_total + $pmf_total;

    // 🔹 4. Return print-ready JSON
    return response()->json([
        'or_no'  => $record->or_no,
        'agency' => $record->agency,
        'owner'  => $record->owner,
        'charges' => [
            'cf'    => round($cf_total, 2),
            'sf'    => round($sf_total, 2),
            'spf'   => round($spf_total, 2),
            'pmf'   => round($pmf_total, 2),
            'total' => round($total, 2),
        ]
    ], 200);
}


}
