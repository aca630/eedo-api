<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SlaughterPrivate; // ✅ use your existing model
use Illuminate\Support\Facades\DB;

class PrivateTransactionController extends Controller
{
    public function store(Request $request)
    {
        /**
         * VALIDATION RULES
         *
         * IMPORTANT NOTES:
         * - small_heads and large_heads remain the OFFICIAL basis for computation
         * - animal breakdown and kilos are IDENTIFICATION ONLY
         * - all new fields are OPTIONAL (nullable)
         * - PMF is a FLAT fee per transaction
         */

        $validated = $request->validate([

            // 🔹 Transaction header
            'date'   => 'required|date',
            'or_no'  => 'required|string|max:255',
            'agency' => 'required|string|max:255',
            'owner'  => 'required|string|max:255',

            // 🔹 Official computation basis (manual)
            'small_heads' => 'nullable|integer|min:0',
            'large_heads' => 'nullable|integer|min:0',

            // 🔹 SMALL animal identification (manual input)
            'small_kilos' => 'nullable|integer|min:0',
            'goat_heads'  => 'nullable|integer|min:0',
            'hog_heads'   => 'nullable|integer|min:0',

            // 🔹 LARGE animal identification (manual input)
            'large_kilos'  => 'nullable|integer|min:0',
            'cow_heads'    => 'nullable|integer|min:0',
            'carabao_heads'=> 'nullable|integer|min:0',

            // 🔹 Flat Post Mortem Fee (per transaction)
            'pmf' => 'nullable|integer|min:0',
        ]);

        /**
         * SAVE TRANSACTION
         *
         * - Uses mass assignment via SlaughterPrivate::$fillable
         * - All validated fields will be saved automatically
         * - Fields not present in payload will be stored as NULL
         */

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
    // 🔹 1. Get latest private transaction
    $record = SlaughterPrivate::orderByDesc('id')->first();

    if (!$record) {
        return response()->json([
            'success' => false,
            'message' => 'No records found.'
        ], 404);
    }

    // 🔹 2. Get livestock charges (used ONLY for computation)
    $charges = DB::table('livestock_charges')->get();

    $small = $charges->where('livestock_id', 1)->first(); // small animals
    $large = $charges->where('livestock_id', 2)->first(); // large animals

    if (!$small || !$large) {
        return response()->json([
            'success' => false,
            'message' => 'Livestock charges not found.'
        ], 404);
    }

    // 🔹 3. Heads count (used for computation)
    $small_heads = (int) ($record->small_heads ?? 0);
    $large_heads = (int) ($record->large_heads ?? 0);

    // 🔹 4. Compute fees (PER HEAD)
    $cf_total  = ($small_heads * $small->cf)  + ($large_heads * $large->cf);
    $sf_total  = ($small_heads * $small->sf)  + ($large_heads * $large->sf);
    $spf_total = ($small_heads * $small->spf) + ($large_heads * $large->spf);

    // 🔹 5. PMF is FLAT (manual input)
    $pmf_total = (int) ($record->pmf ?? 0);

    // 🔹 6. Grand total
    $total = $cf_total + $sf_total + $spf_total + $pmf_total;

    // 🔹 7. Return PRINT-READY response
    return response()->json([
        'success' => true,

        // 🧾 BASIC INFO
        'or_no'  => $record->or_no,
        'agency' => $record->agency,
        'owner'  => $record->owner,

        // 🐄 HEAD COUNTS (used in computation)
        'heads' => [
            'small' => $small_heads,
            'large' => $large_heads,
        ],

        // 🐾 ANIMAL DETAILS (REFERENCE ONLY – NO COMPUTATION)
        'animals' => [
            // SMALL ANIMALS
            'small_kilos' => (int) ($record->small_kilos ?? 0),
            'goat_heads'  => (int) ($record->goat_heads ?? 0),
            'hog_heads'   => (int) ($record->hog_heads ?? 0),

            // LARGE ANIMALS
            'large_kilos'   => (int) ($record->large_kilos ?? 0),
            'cow_heads'     => (int) ($record->cow_heads ?? 0),
            'carabao_heads' => (int) ($record->carabao_heads ?? 0),
        ],

        // 💰 FEES
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
