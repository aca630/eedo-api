<?php

namespace App\Http\Controllers\Api\POS;

use App\Http\Controllers\Api\Helpers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class POSReportsController extends BaseController
{
    //
    public function CashPerCashier(Request $request)
    {
        Log::channel('stderr')->info('boom');
        $date = $request->date;
        // $to = $request->to;
        $cashierId = $request->cashierId;

        Log::channel('stderr')->info($date);

        $data = DB::table('purchase_orders')
            ->select(
                'cashier_id',
                DB::raw('SUM(quantity * retail_price) as total_cash'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT purchase_order_id) as total_orders')
            )
            ->where('cashier_id', $cashierId)
            ->where('isvoided', 0)
            ->whereDate('created_at', $date)
            ->groupBy('cashier_id')
            ->first();

        return response()->json([
            'cashierId' => $cashierId,
            'total_cash' => $data->total_cash ?? 0,
            'total_quantity' => $data->total_quantity ?? 0,
            'total_orders' => $data->total_orders ?? 0,
        ]);
    }
}
