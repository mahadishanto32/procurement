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

class GRNStockInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title="QC List";
        try{
        $goodsReceiveItems=GoodsReceivedItemStockIn::where('is_grn_complete','no')->where('received_qty', '>', 0)->pluck('goods_received_item_id')->all();

        $purchaseOrder = PurchaseOrder::with('relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')
            ->where('is_send','yes')
            ->when(isset(auth()->user()->employee->as_unit_id), function($query){
                    return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
                })
            ->whereHas('relGoodReceiveNote',function ($query){
                $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
            })
            ->whereHas('relGoodReceiveNote.relGoodsReceivedItems', function($query) use($goodsReceiveItems){
                return $query->whereIn('id',$goodsReceiveItems);
            })
            ->orderBy('id','desc')
            ->paginate(30);

        return view('pms.backend.pages.grn-stock-in.qce-list',compact('purchaseOrder','title'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function grnList()
    {
        $title="GRN List";
        try{
            $goodsReceiveItems=GoodsReceivedItemStockIn::where('is_grn_complete','yes')->pluck('goods_received_item_id')->all();

            $purchaseOrder = PurchaseOrder::with('relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')
            ->when(isset(auth()->user()->employee->as_unit_id), function($query){
                    return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
                })
            ->where('is_send','yes')
            ->whereHas('relGoodReceiveNote',function ($query){
                $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
            })
            ->whereHas('relGoodReceiveNote.relGoodsReceivedItems', function($query) use($goodsReceiveItems){
                return $query->whereIn('id',$goodsReceiveItems);
            })
            ->orderBy('id','desc')
            ->paginate(30);

        return view('pms.backend.pages.grn-stock-in.grn-list',compact('purchaseOrder','title'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
    
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function grnStockInList($id)
     {
        $title="GRN Stock In List";

        try{

            $goodsReceiveItems=GoodsReceivedItem::where('goods_received_note_id',$id)
                                ->pluck('id')
                                ->all();

            $grn_stock_in_lists=GoodsReceivedItemStockIn::whereIn('goods_received_item_id',$goodsReceiveItems)
                                ->where('is_grn_complete','no')
                                ->where('received_qty','>', 0)
                                ->orderby('goods_received_item_id','desc')
                                ->get();

            $warehouse_ids=Auth::user()->relUsersWarehouse->pluck('id')->all();
            
            if(count($warehouse_ids)>0) {
               $warehouses = Warehouses::whereIn('id',$warehouse_ids)->select('name','id')->get();
            }else{
                $warehouses = Warehouses::select('name','id')->get();
            }

            return view('pms.backend.pages.grn-stock-in.grn-stock-in-list',compact('grn_stock_in_lists','title','warehouses','id'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
     }
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function store(Request $request)
     { 
        
        DB::beginTransaction();
        try {
            foreach($request->id as $key=>$value){

                if ($request->warehouse_id[$key]) {
                    $wareHouse=Warehouses::findOrFail($request->warehouse_id[$key]);
                }

                $model=GoodsReceivedItemStockIn::where('id',$key)->where('is_grn_complete','no')->first();

                if(count((array)$model)>0)
                {
                    $product=Product::findOrFail($model->relGoodsReceivedItems->product_id);
                    new InventoryActionControl($product,$wareHouse,$model->total_amount,$model->received_qty,'active',$model->relGoodsReceivedItems->relGoodsReceivedNote->grn_reference_no);

                    $model->update(['is_grn_complete'=>'yes','warehouse_id'=>$wareHouse->id]);

                }else{
                    return $this->backWithError('Data not found.!!');
                }
            }


            $goodsReceivedNote = GoodsReceivedNote::where('id',$request->goods_received_note_id)->first();
            //Notification
            $message = '<span class="notification-links" data-src="'.route('pms.purchase.order-list.show',$goodsReceivedNote->purchase_order_id).'?view" data-title="Purchase Order Details">Reference No:'.$goodsReceivedNote->relPurchaseOrder->reference_no.'. Waiting for the Billing.</span>';

            CreateOrUpdateNotification('',getManagerInfo('Billing'),$message,'unread','send-to-billing','');

            DB::commit();

            return redirect('pms/supplier/rating/'.$goodsReceivedNote->relPurchaseOrder->relQuotation->supplier_id.'/'.$goodsReceivedNote->id)->with(['message'=>'GRN Successful','alert-type'=>'success']);

        }catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }

    public function grnSlip($id)
    {
        try{
            $title = "GRN Slip";
            $note = GoodsReceivedNote::findOrFail($id);
            $items = GoodsReceivedItem::where('goods_received_note_id', $note->id)
            ->whereHas('relGoodsReceivedItemStockIn', function($query){
                return $query->where('is_grn_complete', 'yes');
            })->get();

            $data = [
                'title' => $title,
                'note' => $note,
                'items' => $items
            ];

            //return viewMPDF('pms.backend.pages.grn-stock-in.grn-slip-pdf', $data, $title, $title);

            return view('pms.backend.pages.grn-stock-in.grn-slip', $data);

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
}
