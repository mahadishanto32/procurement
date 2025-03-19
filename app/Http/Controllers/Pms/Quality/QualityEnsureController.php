<?php

namespace App\Http\Controllers\Pms\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use App\Models\PmsModels\InventoryModels\InventoryActionControl;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Warehouses;
use App\Models\PmsModels\PurchaseReturn;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Purchase\PurchaseOrderItem;
use App\Models\PmsModels\Grn\GoodsReceivedNote;
use App\Models\PmsModels\Grn\GoodsReceivedSummary;
use App\Models\PmsModels\Grn\GoodsReceivedItem;
use App\Models\PmsModels\Grn\GoodsReceivedItemStockIn;
use App\Models\PmsModels\Grn\Faq;
use App\Models\PmsModels\Grn\ReturnChangeFaq;

use Illuminate\Support\Facades\Mail;
use DB,Auth,Session,redirect;

class QualityEnsureController extends Controller
{
    /**
     * Display a approved listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $title="Quality Ensure Approval List";

            $model = PurchaseOrder::where('is_send','yes')
            ->whereHas('relGoodReceiveNote.relGoodsReceivedItems',function ($query){
                return $query->where('quality_ensure','approved');
            })
            ->when(isset(auth()->user()->employee->as_unit_id), function($query){
                return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
            })
            ->when(auth()->user()->hasRole('Department-Head'), function($query){
                return $query->whereHas('purchaseOrderRequisitions', function($query){
                    return $query->where('hr_department_id', auth()->user()->employee->as_department_id);
                });
            });
            if (isset($request->po_id)){
                $title.=" for P.O. Reference: $single->reference_no";
                $model->where('id',$request->po_id);
            }
            
            $purchaseOrder=$model->orderBy('id','desc')->paginate(30);

            return view('pms.backend.pages.quality.approved-index',compact('title','purchaseOrder'));
        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
 }

   /**
     * Show the Grn Wise Approved Item List.
     *
     * @return \Illuminate\Http\Response
     */

    public function ensureCheck($id)
    {
        try{
            $title="Quality Ensure Check";

            $grn = GoodsReceivedNote::with('relPurchaseOrder','relPurchaseOrder.relQuotation','relGoodsReceivedItems','relGoodsReceivedItems.relProduct','relGoodsReceivedItems.relPurchaseOrderReturns')
            ->where('id',$id)
            ->first();
            
            $grn->rel_goods_received_items = $grn->relGoodsReceivedItems()->whereIn('quality_ensure',['pending'])->get();

            $wareHouses = Warehouses::select('name','id')->get();

            $faqs=Faq::where('status','active')->get();

            return view('pms.backend.pages.quality.pending-index',compact('title','grn','wareHouses','faqs'));
        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function getFaqs($category_id)
    {
        $questions = Faq::where('category_id', $category_id)->where('status','active')->get();
        $data = '';
        if(isset($questions[0])){
            foreach($questions as $key => $question){
                $data .='<li>
                    <input class="form-check-input" type="checkbox" name="faq_id[]" id="faq_'.$question->id.'" value="'.$question->id.'" required>
                    <label class="form-check-label" for="faq_'.$question->id.'"><strong>'.$question->name.'</strong>
                    </label>
                </li>';
            }
        }

        return $data;
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {

        $model = GoodsReceivedItem::findOrFail($request->id);

        $product = Product::findOrFail($model->product_id);

        if(isset($model->id) && $request->quality_ensure==='approved'){

            DB::beginTransaction();
            try{
                
                $newText = 'Approved';
                $update=$model->update([
                    'quality_ensure' => $request->quality_ensure,
                    'received_qty' =>  number_format($model->qty,2),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id
                ]);

                $prefix='QE-AP-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
                $refNo=uniqueCode(18,$prefix,'goods_received_items_stock_in','id');

                $GRItemsStockIn = new GoodsReceivedItemStockIn();
                $GRItemsStockIn->purchase_order_id = $model->relGoodsReceivedNote->purchase_order_id;
                $GRItemsStockIn->goods_received_item_id = $model->id;
                $GRItemsStockIn->reference_no = $refNo;
                $GRItemsStockIn->unit_amount = $model->unit_amount;
                $GRItemsStockIn->received_qty = $model->qty;
                $GRItemsStockIn->sub_total = $model->sub_total;
                $GRItemsStockIn->discount_percentage = $model->discount_percentage;
                $GRItemsStockIn->discount = $model->discount;
                $GRItemsStockIn->vat_percentage = $model->vat_percentage;
                $GRItemsStockIn->vat = $model->vat;
                $GRItemsStockIn->total_amount = $model->total_amount;
                $GRItemsStockIn->is_grn_complete = 'no';
                $GRItemsStockIn->save();

                $message = '<span class="notification-links" data-src="'.route('pms.grn.grn-process.show',$model->relGoodsReceivedNote->id).'?view" data-title="Gate-In Details">Reference No:'.$model->relGoodsReceivedNote->reference_no.'. Waiting for the GRN.</span>';

                CreateOrUpdateNotification('',getManagerInfo('Store-Manager',$model->relGoodsReceivedNote->relPurchaseOrder->hr_unit_id),$message,'unread','send-to-store');

                DB::commit();

                return response()->json([
                    'success' => true,
                    'new_text' => $newText,
                    'message' => 'Successfully Updated this Item Quality Status!!'
                ]);
            }catch (\Throwable $th){
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => $th->getMessage()
                ]);
            }

        }elseif(isset($model->id) && $request->quality_ensure==='return-change' || $request->quality_ensure==='return'){

            if (($model->qty) < $request->return_qty) {
                return $this->backWithWarning('Your return qty is greater then maximum qty');
            }

            if ($request->return_qty <= 0) {
               return $this->backWithWarning('Minimum One item is required');
            }

            $code=$request->quality_ensure=='return-change'?'QE-RP-':'QE-RT-';

            $prefix=$code.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $refNo=uniqueCode(18,$prefix,'goods_received_items_stock_in','id');

            DB::beginTransaction();
            try{
                $update=$model->update([
                    'quality_ensure' => $request->quality_ensure,
                    'received_qty' =>  $model->qty-$request->return_qty,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id
                ]);

                if ($update) {
                    PurchaseReturn::create([
                        'goods_received_item_id'=>$model->id,
                        'return_note' => $request->return_note,
                        'return_qty' => $request->return_qty,
                        'status' => $request->quality_ensure,
                    ]);

                    //subtotal,received_qty,discount_amount,vat_amount
                    $receivedQty=$model->qty-$request->return_qty;
                    if($receivedQty > 0){
                        $subtotal=$receivedQty*$model->unit_amount;
                        $discountAmount= ($model->discount_percentage * $subtotal)/100;
                        $vatAmount= ($model->vat_percentage * $subtotal)/100;

                        $GRItemsStockIn = new GoodsReceivedItemStockIn();
                        $GRItemsStockIn->purchase_order_id = $model->relGoodsReceivedNote->purchase_order_id;
                        $GRItemsStockIn->reference_no = $refNo;
                        $GRItemsStockIn->goods_received_item_id = $model->id;
                        $GRItemsStockIn->unit_amount = $model->unit_amount;
                        $GRItemsStockIn->received_qty = $receivedQty;
                        $GRItemsStockIn->sub_total = $subtotal;
                        $GRItemsStockIn->discount_percentage = $model->discount_percentage;
                        $GRItemsStockIn->discount = $discountAmount;
                        $GRItemsStockIn->vat_percentage = $model->vat_percentage;
                        $GRItemsStockIn->vat = $vatAmount;
                        $GRItemsStockIn->total_amount = ($subtotal-$discountAmount)+$vatAmount;
                        $GRItemsStockIn->is_grn_complete = 'no';
                        $GRItemsStockIn->save();
                    }

                    //Insert return reason
                    if (!empty($request->faq_id)) {
                        foreach ($request->faq_id as $key => $faq_id) {
                            ReturnChangeFaq::create([
                                'faq_id'=>$faq_id,
                                'goods_received_item_id'=>$model->id
                            ]);
                        }
                    }
                }
                
                $message = '<span class="notification-links" data-src="'.route('pms.grn.grn-process.show',$model->relGoodsReceivedNote->id).'?view" data-ttile="Gate-In Details">Reference No:'.$model->relGoodsReceivedNote->reference_no.'. Waiting for the GRN.</span>';

                CreateOrUpdateNotification('',getManagerInfo('Store-Manager',$model->relGoodsReceivedNote->relPurchaseOrder->hr_unit_id),$message,'unread','send-to-store');

                DB::commit();

                return $this->backWithSuccess('Successfully Updated this Item Quality Status!!');

            }catch (Throwable $th){
                DB::rollback();
                return $this->backWithError($th->getMessage());
            }
        }

        return back();
    }


    /**
    * Show the Grn Wise Approved Item List.
    *
    * @return \Illuminate\Http\Response
    */

    public function grnWiseApprovedItemList($id)
    {
        try{

            $title="Quality Ensure Approval List";
            $goodsReceivedItemId = GoodsReceivedItem::where('goods_received_note_id',$id)->where('quality_ensure','approved')->pluck('id')->all();

            $approval_list = GoodsReceivedItemStockIn::whereIn('goods_received_item_id',$goodsReceivedItemId)->orderBy('id','desc')->paginate(30);


            return view('pms.backend.pages.quality.approved-list',compact('title','approval_list'));
        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }


    /**
    * Show the Grn Wise Return List.
    *
    * @return \Illuminate\Http\Response
    */


    public function returnlList()
    {
        try{
            $title="Quality Ensure Return List";

            $model = PurchaseOrder::where('is_send','yes')
            ->when(isset(auth()->user()->employee->as_unit_id), function($query){
                return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
            })
            ->when(auth()->user()->hasRole('Department-Head'), function($query){
                return $query->whereHas('purchaseOrderRequisitions', function($query){
                    return $query->where('hr_department_id', auth()->user()->employee->as_department_id);
                });
            })
            ->whereHas('relGoodReceiveNote.relGoodsReceivedItems',function ($query){
                return $query->where('quality_ensure','return');
            })
            ->orderBy('id', 'desc');
                
            if (isset($request->po_id)){
                $title.=" for P.O. Reference: $single->reference_no";
                $model->where('id',$request->po_id);
            }
            
            $purchaseOrder = $model->paginate(30);

            return view('pms.backend.pages.quality.return-index',compact('title','purchaseOrder'));
        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
    * Show the Grn Wise Single Return Item List.
    *
    * @return \Illuminate\Http\Response
    */

    public function grnWiseReturnItemList($id)
    {
        try{

            $title="Quality Ensure Return List";
            $goodsReceivedItemId = GoodsReceivedItem::where('goods_received_note_id',$id)->where('quality_ensure','return')->pluck('id')->all();
            $returnList = PurchaseReturn::whereIn('goods_received_item_id',$goodsReceivedItemId)->orderBy('id','desc')->paginate(30);

            return view('pms.backend.pages.quality.return-list',compact('title','returnList'));
        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }


    /**
    * Show the Grn Wise Return Change List.
    *
    * @return \Illuminate\Http\Response
    */

    public function returnChangeList()
    {
        try{

            $title="Quality Ensure Return Replace List";

            $model = PurchaseOrder::where('is_send','yes')
            ->when(isset(auth()->user()->employee->as_unit_id), function($query){
                return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
            })
            ->when(auth()->user()->hasRole('Department-Head'), function($query){
                return $query->whereHas('purchaseOrderRequisitions', function($query){
                    return $query->where('hr_department_id', auth()->user()->employee->as_department_id);
                });
            })
            ->whereHas('relGoodReceiveNote.relGoodsReceivedItems',function ($query){
                return $query->where('quality_ensure','return-change');
            });
                
            if (isset($request->po_id)){
                $title.=" for P.O. Reference: $single->reference_no";
                $model->where('id',$request->po_id);
            }
            
            $purchaseOrder=$model->orderBy('id','desc')->paginate(30);

            return view('pms.backend.pages.quality.return-change-index',compact('title','purchaseOrder'));

        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
    * Show the Grn Wise Return Item List.
    *
    * @return \Illuminate\Http\Response
    */
    public function grnWiseReturnChangeItemList($id)
    {
        try{

            $title="Quality Ensure Return Replace List";

            $changed = PurchaseReturn::whereHas('relGoodsReceivedItems', function($query) use($id){
                return $query->where('goods_received_note_id', $id);
            })->where('status', 'return-change')->get();

            return view('pms.backend.pages.quality.return-change-list',compact('title','changed'));

        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function approvedItemPrint($id,$type)
    {
         try{

            $title="Quality Ensure Approved Print View";

            $quotation=GoodsReceivedNote::findOrFail($id);

            $goodsReceivedItemId = GoodsReceivedItem::where('goods_received_note_id',$id)->where('quality_ensure',$type)->pluck('id')->all();

            $approval_list = GoodsReceivedItemStockIn::whereIn('goods_received_item_id',$goodsReceivedItemId)->get();
            
            return view('pms.backend.pages.quality.approved-item-print-view',compact('title','approval_list','quotation'));

        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function returnItemPrintView($id,$type)
    {
         try{

            $title="Quality Ensure Return Print View";

            $quotation=GoodsReceivedNote::findOrFail($id);

            $goodsReceivedItemId = GoodsReceivedItem::where('goods_received_note_id',$id)->where('quality_ensure','return')->pluck('id')->all();


            if ($type=='return-approved-list') {
                $approved = GoodsReceivedItemStockIn::whereIn('goods_received_item_id',$goodsReceivedItemId)->get();
                return view('pms.backend.pages.quality.return-approved-item-print-view',compact('title','approved','quotation'));
            }else{
                $returned = PurchaseReturn::whereIn('goods_received_item_id',$goodsReceivedItemId)->where('status', 'return')->get();
                return view('pms.backend.pages.quality.return-item-print-view',compact('title','returned','quotation'));
            }
            

        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function returnReplaceItemPrintView($id,$type)
    {
         try{

            $title="Quality Ensure Return Print View";

            $quotation=GoodsReceivedNote::findOrFail($id);

            $returnChangeList = GoodsReceivedItem::where('goods_received_note_id',$id)
            ->where('quality_ensure','return-change')->get();

            if ($type=='return-change-list') {
                return view('pms.backend.pages.quality.return-replace-return-item-print-view',compact('title','returnChangeList','quotation'));
            }else{

                return view('pms.backend.pages.quality.return-replace-item-print-view',compact('title','returnChangeList','quotation'));
            }



        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function returnChangeReceived(Request $request)
    {    

        try{

            DB::beginTransaction();
            foreach($request->id as $key=>$id){

                $model=GoodsReceivedItem::where(['id'=>$id,'quality_ensure'=>'return-change'])->first();
                $product=Product::findOrFail($model->product_id);


                $prefix='QE-RRP-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
                $refNo=uniqueCode(18,$prefix,'goods_received_items_stock_in','id');


                if(isset($model->id) && $request->status==='received'){
                    if (($model->qty-$model->received_qty) < $request->received_qty[$key]) {
                        return $this->backWithWarning('Your return qty is greater then maximum qty');
                    }

                    $totalReceivedQty = $model->received_qty+$request->received_qty[$key];

                    $qualityEnsure=($totalReceivedQty==$model->qty)?'approved':'return-change';

                    $update=$model->update([
                        'quality_ensure' => $qualityEnsure,
                        'received_qty' =>  $totalReceivedQty,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->id
                    ]);

                    if ($update) {
                        PurchaseReturn::create([
                            'goods_received_item_id'=>$model->id,
                            'return_note' => $request->return_note,
                            'return_qty' => $request->received_qty[$key],
                            'status' => $request->status,
                        ]);

                        $receivedQty=$request->received_qty[$key];
                        $subtotal=$receivedQty*$model->unit_amount;
                        $discountAmount= ($model->discount_percentage * $subtotal)/100;
                        $vatAmount= ($model->vat_percentage * $subtotal)/100;

                        $GRItemsStockIn = new GoodsReceivedItemStockIn();
                        $GRItemsStockIn->purchase_order_id = $model->relGoodsReceivedNote->purchase_order_id;
                        $GRItemsStockIn->reference_no = $refNo;
                        $GRItemsStockIn->goods_received_item_id = $model->id;
                        $GRItemsStockIn->unit_amount = $model->unit_amount;
                        $GRItemsStockIn->received_qty = $receivedQty;
                        $GRItemsStockIn->sub_total = $subtotal;
                        $GRItemsStockIn->discount_percentage = $model->discount_percentage;
                        $GRItemsStockIn->discount = $discountAmount;
                        $GRItemsStockIn->vat_percentage = $model->vat_percentage;
                        $GRItemsStockIn->vat = $vatAmount;
                        $GRItemsStockIn->total_amount = ($subtotal-$discountAmount)+$vatAmount;
                        $GRItemsStockIn->is_grn_complete = 'no';
                        $GRItemsStockIn->save();
                    }
                }
            }

            $message = '<span class="notification-links" data-src="'.route('pms.grn.grn-process.show',$model->relGoodsReceivedNote->id).'?view" data-title="Gate-In Details">Reference No:'.$model->relGoodsReceivedNote->reference_no.'. Waiting for the GRN.</span>';

            CreateOrUpdateNotification('',getManagerInfo('Store-Manager',$model->relGoodsReceivedNote->relPurchaseOrder->hr_unit_id), $message,'unread','send-to-store','');

            DB::commit();
            return $this->redirectBackWithSuccess('Successfully Updated this Item Quality Status!!','pms.quality.ensure.return.change.list');

        }catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }
}
