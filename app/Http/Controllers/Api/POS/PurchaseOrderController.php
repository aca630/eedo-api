<?php

namespace App\Http\Controllers\Api\POS;

use App\Http\Controllers\Api\Helpers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\purchase_orders;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends BaseController
{
    //
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {

            $items = $request->all();

            if (empty($items) || !is_array($items)) {
                return response()->json([
                    'message' => 'No items found.'
                ], 422);
            }

            $now = now();

            $cashierId = $items[0]['cashierId'];

            $purchase_order_id = 'PO-' . $cashierId . '' . now()->format('mdyHis');

            $productIds = collect($items)
                ->pluck('id')
                ->unique()
                ->values();

            $products = Products::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $insertRows = [];
            $responseItems = [];
            foreach ($items as $item) {

                $product = $products[$item['id']] ?? null;

                if (!$product) {
                    throw new Exception('Product not found: ' . $item['id']);
                }

                $qty = (int) $item['quantity'];

                if ($qty <= 0) {
                    throw new Exception('Invalid quantity');
                }

                $product->stock -= $qty;

                $insertRows[] = [
                    'purchase_order_id' => $purchase_order_id,
                    'cashier_id'        => $item['cashierId'],
                    'product_id'        => $item['id'],
                    'quantity'          => $qty,
                    'cost_price'        => $item['cost_price'],
                    'retail_price'      => $item['retail_price'],
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];

                $responseItems[] = [
                    'purchase_order_id' => $purchase_order_id,
                    'quantity_purchase' => $qty,

                    'product' => [
                        'id'            => $product->id,
                        'product_code'  => $product->product_code,
                        'barcode'       => $product->barcode,
                        'name'          => $product->name,
                        'description'   => $product->description,
                        'category_id'   => $product->category_id,
                        'cost_price'    => $item['cost_price'],
                        'retail_price'  => $item['retail_price'],
                        'quantity'      => $item['quantity'],
                        'unit'          => $product->unit,
                        'is_active'     => $product->is_active,
                    ],
                ];
            }

            purchase_orders::insert($insertRows);

            foreach ($products as $product) {
                Products::where('id', $product->id)
                    ->update([
                        'stock' => $product->stock
                    ]);
            }

            return response()->json([
                'message' => 'Purchase order saved successfully.',
                'purchase_order_id' => $purchase_order_id,
                'items' => $responseItems,
            ]);
        });
    }

public function show($purchaseOrderId)
{
    $items = purchase_orders::query()
        ->join('products', 'products.id', '=', 'purchase_orders.product_id')
        ->where('purchase_orders.purchase_order_id', $purchaseOrderId)
        ->select(
            'purchase_orders.*',
            'products.name'
        )
        ->get();

    if ($items->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Purchase order not found',
        ], 404);
    }

    $isVoided = (int) $items->first()->isvoided === 1;

    return response()->json([
        'success' => true,
        'purchase_order_id' => $purchaseOrderId,
        'isvoided' => $isVoided,
        'items' => $items,
        'total_amount' => $items->sum(
            fn ($x) => $x->quantity * $x->retail_price
        ),
        'total_quantity' => $items->sum('quantity'),
    ]);
}

public function update(Request $request, $purchaseOrderId)
{

    purchase_orders::where('purchase_order_id', $purchaseOrderId)
        ->update([
            'isvoided' => 1,
        ]);

    return response()->json([
        'success' => true,
        'message' => 'Purchase Order Voided Successfully',
    ]);
}
}
