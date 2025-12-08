<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SlaughterPrivate; // ✅ use your existing model
use Illuminate\Support\Facades\DB;

class PrivateTransactionController extends Controller
{
    public function store(Request $request)
    {
        // ✅ Validate payload including PMF
        $validated = $request->validate([
            'date'        => 'required|date',
            'or_no'       => 'required|string|max:255',
            'agency'      => 'required|string|max:255',
            'owner'       => 'required|string|max:255',
            'small_heads' => 'nullable|integer|min:0',
            'large_heads' => 'nullable|integer|min:0',
            'pmf'         => 'nullable|integer|min:0', // 👈 NEW
        ]);

        // ✅ Save to slaughter_privates (pmf included)
        $record = SlaughterPrivate::create($validated);

        return response()->json([
            'message' => 'Private transaction saved successfully!',
            'data'    => $record,
        ], 201);
    }


public function latest()
{
    $record = SlaughterPrivate::orderByDesc('id')->first();

    if (!$record) {
        return response()->json([
            'success' => false,
            'message' => 'No records found.'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'id'          => $record->id,
            'date'        => $record->date,
            'or_no'       => $record->or_no,
            'agency'      => $record->agency,
            'owner'       => $record->owner,
            'small_heads' => (int) $record->small_heads,
            'large_heads' => (int) $record->large_heads,
            'pmf'         => (int) ($record->pmf ?? 0), // 👈 ADD
            'created_at'  => $record->created_at,
            'updated_at'  => $record->updated_at,
        ]
    ], 200);
}



public function computeLatest()
{
    // 🔹 1. Get latest record
    $record = SlaughterPrivate::orderByDesc('id')->first();

    if (!$record) {
        return response()->json([
            'success' => false,
            'message' => 'No records found.'
        ], 404);
    }

    // 🔹 2. Get livestock charges
    $charges = DB::table('livestock_charges')->get();

    $small = $charges->where('livestock_id', 1)->first(); // small heads
    $large = $charges->where('livestock_id', 2)->first(); // large heads

    if (!$small || !$large) {
        return response()->json([
            'success' => false,
            'message' => 'Livestock charges not found.'
        ], 404);
    }

    $small_heads = (int) ($record->small_heads ?? 0);
    $large_heads = (int) ($record->large_heads ?? 0);

    // 🔹 3. Compute CF, SF, SPF (per head)
    $cf_total  = ($small_heads * $small->cf)  + ($large_heads * $large->cf);
    $sf_total  = ($small_heads * $small->sf)  + ($large_heads * $large->sf);
    $spf_total = ($small_heads * $small->spf) + ($large_heads * $large->spf);

    // 🔹 4. PMF is FLAT per transaction, not per head
    $pmf_total = (int) ($record->pmf ?? 0);

    // 🔹 5. Grand total
    $total = $cf_total + $sf_total + $spf_total + $pmf_total;

    // 🔹 6. Return print-ready JSON
    return response()->json([
        'success' => true,
        'or_no'   => $record->or_no,
        'agency'  => $record->agency,
        'owner'   => $record->owner,
        'heads'   => [
            'small' => $small_heads,
            'large' => $large_heads,
        ],
        'charges' => [
            'cf'    => $cf_total,
            'sf'    => $sf_total,
            'spf'   => $spf_total,
            'pmf'   => $pmf_total,
            'total' => $total,
        ],
    ], 200);
}



}
