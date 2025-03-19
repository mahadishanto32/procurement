<?php

namespace App\Http\Controllers\Pms\Grn;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\InventoryModels\InventoryActionControl;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Warehouses;
use Illuminate\Http\Request;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Purchase\PurchaseOrderItem;
use App\Models\PmsModels\Grn\GoodsReceivedNote;
use App\Models\PmsModels\Grn\GoodsReceivedItem;
use App\Models\PmsModels\Grn\GoodsReceivedItemStockIn;
use Illuminate\Support\Facades\Mail;
use DB,Auth;

class GateInSlipController extends Controller
{
    public function show($po_id)
    {
        try{
            $grn_id = request()->has('grn') && request()->get('grn') > 0 ? request()->get('grn') : 0;

            $purchaseOrder = PurchaseOrder::has('relGoodReceiveNote')
            ->where([
                'is_send' => 'yes',
                'id' => $po_id
            ])->first();

            $goodReceiveNotes = GoodsReceivedNote::with('relPurchaseOrder','relPurchaseOrder.relQuotation','relGoodsReceivedItems','relGoodsReceivedItems.relProduct')
            ->whereIn('id', ($grn_id > 0 ? [$grn_id] : $purchaseOrder->relGoodReceiveNote->pluck('id')->toArray()))->get();

            // return viewMPDF('pms.backend.pages.grn.slip-pdf', [
            //     'title' => 'Gate in Slip',
            //     'purchaseOrder' => $purchaseOrder,
            //     'goodReceiveNotes' => $goodReceiveNotes,

            // ], 'Gate in Slip', 'Gate in Slip');

            return view('pms.backend.pages.grn.slip',compact('purchaseOrder','goodReceiveNotes'));

        }catch (Exception $e){
            DB::rollback();
            return $this->backWithError($e->getMessage());
        }
    }
}
