<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Purchase\PurchaseOrderItem;
use App\Models\PmsModels\Purchase\PurchaseOrderAttachment;
use App\Models\PmsModels\SupplierPayment;
use App\Models\PmsModels\Grn\GoodsReceivedNote;
use App\Models\PmsModels\Grn\GoodsReceivedSummary;
use App\Models\PmsModels\Grn\GoodsReceivedItem;
use App\Models\PmsModels\InventoryModels\InventoryActionControl;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\SupplierLedgers;
use App\Models\PmsModels\Warehouses;
use App\Models\PmsModels\BillingChalan;
use Illuminate\Support\Facades\Mail;
use DB,Auth;


class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
           $title = "PO List";

            $purchaseOrdersAgainstGrn = PurchaseOrder::where('is_send','yes')->whereHas('relGoodReceiveNote',function ($query){
                $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
            })
            ->when(request()->has('from_date') && !empty(request()->get('from_date')), function($query){
               return $query->whereDate('po_date', '>=', date('Y-m-d',strtotime(request()->get('from_date'))));
            })
            ->when(request()->has('to_date') && !empty(request()->get('to_date')), function($query){
                $query->whereDate('po_date', '<=', date('Y-m-d',strtotime(request()->get('to_date'))));
            })
            ->when(request()->has('search_text') && !empty(request()->get('search_text')), function($query){
                return $query->where(function($queryHead)
                {
                    return $queryHead->where('reference_no', 'LIKE', '%'.request()->get('search_text').'%')
                    ->orWhereHas('relPoAttachment', function($query){
                        return $query->where('bill_number','LIKE', '%'.request()->get('search_text').'%');
                    });
                });
            })
            ->orderBy('id','desc')
            ->paginate(100);

            if (count($purchaseOrdersAgainstGrn)>0){
                calculateGrnQtyAgainstPurchaseOrder($purchaseOrdersAgainstGrn);
            }

            return view('pms.backend.pages.billing.po-list',compact('title','purchaseOrdersAgainstGrn'));

        }catch(\Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }
    }

    public function attachmentUploadForm(Request $request)
    {
        $po = PurchaseOrder::findOrFail($request->id);
        $po_bill = PurchaseOrderAttachment::where('purchase_order_id',$request->id)->where('bill_type', 'po')->where('status', 'pending')->first();
        return view('pms.backend.pages.billing._po-attachement-upload', compact('po', 'po_bill'));
    }

    public function poInvoiceList($id)
    {
        try{
            $title="Purchase Order Wise Challan List";

            $purchaseOrder=PurchaseOrder::findOrFail($id);

            return view('pms.backend.pages.billing.po-invoice-list',compact('title','purchaseOrder'));
        }catch(\Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }
    }

    public function auditPoInvoiceList($id)
    {
        try{

            $title="Purchase Order Wise Challan List";

            $purchaseOrder=PurchaseOrder::findOrFail($id);

            $ledger=SupplierLedgers::query();
            $poAttachment=PurchaseOrderAttachment::query();
            $goodsReceivedItem=GoodsReceivedItem::query();

            return view('pms.backend.pages.billing.audit-po-invoice-list',compact('title','purchaseOrder','ledger','poAttachment','goodsReceivedItem'));
        }catch(\Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }
    }

    public function poInvoicePrint($id)
    {
        try{

            $title = "Purchase Order";
            $purchaseOrder = PurchaseOrder::findOrFail($id);
            $deliveryContact = \App\User::find(getManagerInfo('Store-Manager', $purchaseOrder->hr_unit_id));
            
            // return viewMPDF('pms.backend.pages.billing.po-invoice-pdf', [
            //     'title' => $title,
            //     'purchaseOrder' => $purchaseOrder,
            //     'deliveryContact' => $deliveryContact

            // ], 'Purchase Order', 'Purchase Order');
            
            return view('pms.backend.pages.billing.po-invoice-print',compact('title','purchaseOrder', 'deliveryContact'));
        }catch(\Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }
    }

    public function billingPOAttachmentList()
    {
        try{

            $title="Billing Attachment List";

            $purchase_order = PurchaseOrder::with('relPurchaseOrderItems','relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems','relPoAttachment')->where('is_send','yes')->whereHas('relGoodReceiveNote',function ($query){
                $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
            })
            ->when(request()->has('from_date') && !empty(request()->get('from_date')), function($query){
             return $query->whereDate('po_date', '>=', date('Y-m-d',strtotime(request()->get('from_date'))));
            })
            ->when(request()->has('to_date') && !empty(request()->get('to_date')), function($query){
                $query->whereDate('po_date', '<=', date('Y-m-d',strtotime(request()->get('to_date'))));
            })
            ->when(request()->has('search_text') && !empty(request()->get('search_text')), function($query){
                return $query->where('reference_no', 'LIKE', '%'.request()->get('search_text').'%');
            })
             ->when(request()->has('status') && !empty(request()->get('status')), function($query){
              return $query->whereHas('relPoAttachment', function($query){
                        return $query->where('status',request()->get('status'));
                    });
            })
            ->orderBy('id','desc')
            ->paginate(100);

            if (count($purchase_order)>0){
                calculateGrnQtyAgainstPurchaseOrder($purchase_order);
            }

            return view('pms.backend.pages.billing.po-attachment-list',compact('title','purchase_order'));
        }catch(\Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attachmentUpload(Request $request)
    {
        $this->validate($request, [
            'purchase_order_id'  => "required|max:100",
            'bill_amount'  => "required|max:100",
            'invoice_file' => 'mimes:jpeg,jpg,png,gif,pdf|required|max:5048',
            'vat_challan_file' => 'mimes:jpeg,jpg,png,gif,pdf|required|max:5048'
        ]);

        $model=PurchaseOrder::where('id',$request->purchase_order_id)->first();
        if (empty($model)) {
            return $this->backWithError('Purchase Order Not Found!!');
        }

        $bill_amount = doubleval(str_replace(',','',$request->bill_amount));

        $grn_amount = $model->relGoodsReceivedItemStockIn()->where('is_grn_complete','yes')->sum('total_amount');

       
        if ($bill_amount > $grn_amount){
            return $this->backWithError('Your input amount is greater then GRN amount.');
        }

        //begain transaction
        DB::beginTransaction();
        try{

            $invoice_file='';
            $vat_challan_file='';

            $exising_attachment=PurchaseOrderAttachment::where(['purchase_order_id'=>$request->purchase_order_id,'bill_type'=>'po','status'=>'pending'])->first();

            if ($request->hasFile('invoice_file') || $request->hasFile('vat_challan_file'))
            {   

                if (isset($exising_attachment) && !empty($exising_attachment->invoice_file)) {
                    if(file_exists(public_path($exising_attachment->invoice_file))){
                        unlink(public_path($exising_attachment->invoice_file));   
                    }

                    if(file_exists(public_path($exising_attachment->vat_challan_file))){
                        unlink(public_path($exising_attachment->vat_challan_file));   
                    }
                }

                $invoice_file=$this->fileUpload($request->file('invoice_file'),'upload/purchase-order/invoice');

                $vat_challan_file=$this->fileUpload($request->file('vat_challan_file'),'upload/purchase-order/vat-chalan');
            }

            $PurchaseOrderAttachment=PurchaseOrderAttachment::updateOrCreate(
                [
                    'purchase_order_id' =>  $request->purchase_order_id,
                    'bill_type' => 'po'
                ],
                [
                    'purchase_order_id'=>$request->purchase_order_id,
                    'invoice_file'=>$invoice_file,
                    'vat_challan_file'=>$vat_challan_file,
                    'bill_amount'=>$bill_amount,
                    'bill_number'=>$request->bill_number,
                    'status'=>'pending',
                    'created_by'=>\Auth::user()->id,
                    'updated_by'=>\Auth::user()->id,
                ]
            );

            $supplier_payment=SupplierPayment::updateOrCreate(
                [
                    'supplier_id' => $model->relQuotation->supplier_id,
                    'purchase_order_id' => $request->purchase_order_id,
                    'bill_type' => 'po'
                ],
                [
                    'transection_date' => date('Y-m-d H:i:s'),
                    'transection_type' => 'purchase',
                    'bill_amount' => $bill_amount,
                ]
            );

            $grns = GoodsReceivedNote::doesntHave('relPoAttachment')
            ->where('purchase_order_id', $request->purchase_order_id)
            ->get('id');
            if(isset($grns[0])){
                BillingChalan::where('purchase_order_attachment_id', $PurchaseOrderAttachment->id)->delete();
                foreach($grns as $key => $grn){
                    BillingChalan::create([
                        'purchase_order_attachment_id' => $PurchaseOrderAttachment->id,
                        'goods_received_note_id' => $grn->id
                    ]);
                }
            }

            //Notification
            $message = '<span class="notification-links" data-src="'.route('pms.purchase.order-list.show',$model->id).'?view" data-title="Purchase Order Details">Reference No:'.$model->reference_no.'. Waiting for the Audited.</span>';

            CreateOrUpdateNotification('',getManagerInfo('Audit'),$message,'unread','send-to-audit','');

            //data commit
            DB::commit();

            return $this->backWithSuccess('Successfully uploaded po invoice & vat file.');

        }catch (Exception $e){
            //data rollback if someting wrong
            DB::rollback();
            return $this->backWithError($e->getMessage());
        }

    }

    /**
     * Store a newly created resource in storage.s
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function grnAttachmentUpload(Request $request)
    {
        $this->validate($request, [
            'purchase_order_id'  => "required|max:100",
            'goods_received_note_id'  => "required|max:100",
            'bill_amount'  => "required|max:100",
            'invoice_file' => 'mimes:jpeg,jpg,png,gif,pdf|required|max:5048',
            'vat_challan_file' => 'mimes:jpeg,jpg,png,gif,pdf|required|max:5048'
        ]);

        $model=PurchaseOrder::where('id',$request->purchase_order_id)->first();
        if (empty($model)) {
            return $this->backWithError('Purchase Order Not Found!!');
        }

        $bill_amount=doubleval(str_replace(',','',$request->bill_amount));

        $goodsReceiveItemsId=GoodsReceivedItem::where('goods_received_note_id',$request->goods_received_note_id)
            ->pluck('id')
            ->all();
        
        if (doubleval($model->relGoodsReceivedItemStockIn()
                    ->whereIn('goods_received_item_id',$goodsReceiveItemsId)
                    ->where('is_grn_complete','yes')
                    ->sum('total_amount'))!==$bill_amount){

            return $this->backWithError('Your input amount is greater then GRN amount.');
        }

        //begain transaction
        DB::beginTransaction();
        try{

            $invoice_file='';
            $vat_challan_file='';

            if ($request->hasFile('invoice_file') || $request->hasFile('vat_challan_file'))
            {   
                $exising_attachment=PurchaseOrderAttachment::where(['purchase_order_id'=>$request->purchase_order_id,'goods_received_note_id'=>$request->goods_received_note_id,'bill_type'=>'grn','status'=>'pending'])->first();

                if (isset($exising_attachment) && !empty($exising_attachment->invoice_file)) {
                    if(file_exists(public_path($exising_attachment->invoice_file))){
                        unlink(public_path($exising_attachment->invoice_file));   
                    }

                    if(file_exists(public_path($exising_attachment->vat_challan_file))){
                        unlink(public_path($exising_attachment->vat_challan_file));   
                    }
                }

                $invoice_file=$this->fileUpload($request->file('invoice_file'),'upload/purchase-order/invoice');

                $vat_challan_file=$this->fileUpload($request->file('vat_challan_file'),'upload/purchase-order/vat-chalan');
            }

            $PurchaseOrderAttachment=PurchaseOrderAttachment::updateOrCreate(
                [
                    'purchase_order_id' =>  $request->purchase_order_id,
                    'goods_received_note_id' =>  $request->goods_received_note_id,
                    'bill_type' => 'grn'
                ],
                [
                    'purchase_order_id'=>$request->purchase_order_id,
                    'goods_received_note_id'=>$request->goods_received_note_id,
                    'invoice_file'=>$invoice_file,
                    'vat_challan_file'=>$vat_challan_file,
                    'bill_amount'=>$bill_amount,
                    'bill_type'=>'grn',
                    'bill_number'=>$request->bill_number,
                    'status'=>'pending',
                    'created_by'=>\Auth::user()->id,
                    'updated_by'=>\Auth::user()->id,
                ]
            );

            if(!isset($exising_attachment->purchase_order_id)){
                $supplier_payment = new SupplierPayment();
                $supplier_payment->supplier_id = $model->relQuotation->supplier_id;
                $supplier_payment->purchase_order_id = $request->purchase_order_id;
                $supplier_payment->goods_received_note_id = $request->goods_received_note_id;
                $supplier_payment->transection_date = date('Y-m-d h:i:s');
                $supplier_payment->transection_type = 'purchase';
                $supplier_payment->bill_amount = $bill_amount;
                $supplier_payment->bill_type = "grn";
                $supplier_payment->save();
            }

            $grn = GoodsReceivedNote::find($request->goods_received_note_id);
            BillingChalan::updateOrCreate([
                'purchase_order_attachment_id' => $PurchaseOrderAttachment->id
            ],[
                'goods_received_note_id' => $grn->id
            ]);

            $message = '<span class="notification-links" data-src="'.route('pms.grn.grn-process.show',$grn->id).'?view" data-title="GRN Details">Reference No:'.$grn->reference_no.'. Waiting for the Audited.</span>';

            CreateOrUpdateNotification('',getManagerInfo('Audit'),'Reference No: <strong>'.$grn->reference_no.'</strong>. Waiting for the Audited','unread','send-to-audit','');

            //data commit
            DB::commit();

            return $this->backWithSuccess('Successfully uploaded po invoice & vat file.');

        }catch (Exception $e){
            //data rollback if someting wrong
            DB::rollback();
            return $this->backWithError($e->getMessage());
        }

    }

    public function billingUpdateAction(Request $request)
    {   
        $id=$request->po_id;
        $billType=$request->bill_type;
        $status=$request->status;
        $grnId=isset($request->grn_id)?$request->grn_id:'';

        DB::beginTransaction();
        try {
            PurchaseOrderAttachment::where('purchase_order_id',$id)
            ->when(!empty($grnId), function($query) use($grnId){
                return $query->where('goods_received_note_id', $grnId);
            })
            ->where('bill_type',$billType)
            ->update(['status'=>$status,'remarks'=>$request->remarks]);

            SupplierPayment::where('purchase_order_id',$id)
            ->when(!empty($grnId), function($query) use($grnId){
                return $query->where('goods_received_note_id', $grnId);
            })
            ->where('bill_type',$billType)
            ->update(['status'=>($status=='pending'?'pending':'audited')]);

            $model=PurchaseOrder::where('id',$id)->first();

            $message = '<span class="notification-links" data-src="'.route('pms.purchase.order-list.show',$model->id).'?view" data-title="Purchase Order Details">Reference No:'.$model->reference_no.'. Waiting for Payments.</span>';

            CreateOrUpdateNotification('',getManagerInfo('Accounts'),$message,'unread','send-to-accounts','');

            DB::commit();

            return $this->backWithSuccess('Billing Invoice updated successfully');

        }catch (\Throwable $th){

            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }
}
