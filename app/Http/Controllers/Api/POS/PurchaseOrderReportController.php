<?php

namespace App\Http\Controllers\Api\POS;

use App\Http\Controllers\Api\Helpers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\purchase_orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderReportController extends BaseController
{
    //
     public function index(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $purchaseOrderId = $request->purchase_order_id;

        $query = purchase_orders::query()
            ->join('products', 'products.id', '=', 'purchase_orders.product_id')
            ->select(
                'purchase_orders.purchase_order_id',
                'purchase_orders.cashier_id',
                'purchase_orders.product_id',
                'products.product_code',
                'products.barcode',
                'products.name',
                'products.description',
                'products.unit',
                'purchase_orders.quantity',
                'purchase_orders.cost_price',
                'purchase_orders.retail_price',
                DB::raw('(purchase_orders.quantity * purchase_orders.cost_price) as total_cost'),
                DB::raw('(purchase_orders.quantity * purchase_orders.retail_price) as total_retail'),
                'purchase_orders.created_at'
            );

        if (!empty($purchaseOrderId)) {
            $query->where('purchase_orders.purchase_order_id', $purchaseOrderId);
        }

        if (!empty($from) && !empty($to)) {
            $query->whereBetween('purchase_orders.created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59',
            ]);
        }

        $items = $query
            ->orderBy('purchase_orders.created_at', 'desc')
            ->get();

        return response()->json([
            'purchase_order_id' => $purchaseOrderId,
            'from' => $from,
            'to' => $to,

            'summary' => [
                'total_items' => $items->count(),
                'total_quantity' => $items->sum('quantity'),
                'total_cost' => $items->sum('total_cost'),
                'total_retail' => $items->sum('total_retail'),
            ],

            'data' => $items,
        ]);
    }
}
