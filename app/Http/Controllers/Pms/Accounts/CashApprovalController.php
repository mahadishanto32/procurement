<?php

namespace App\Http\Controllers\Pms\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PmsModels\SupplierPayment;
use App\Models\PmsModels\SupplierLedgers;
use App\Models\PmsModels\Suppliers;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Purchase\PurchaseOrderAttachment;
use App\Models\PmsModels\Grn\GoodsReceivedItem;
use DB;
use Auth;
use App;


class CashApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $cash_status = request()->has('cash_status') ? request()->get('cash_status') : null;
            $is_send = ($cash_status=='pending' || $cash_status=='halt')?'no':null;

            $purchaseOrder = PurchaseOrder::whereHas('relSupplierPayments',function ($query){
                $query->whereRaw('purchase_orders.id=supplier_payments.purchase_order_id');
            })
            ->whereHas('relQuotation',function ($query){
                $query->where('type','direct-purchase')->where('status','active')
                        ->where('is_approved','approved')->where('is_po_generate','yes');
            })
            ->when(!empty($cash_status), function($query) use($cash_status){
                    return $query->where('cash_status', $cash_status);
            })
            ->when(!empty($is_send), function($query) use($is_send){
                    return $query->where('is_send', $is_send);
            })
            ->paginate(100);

            $data = [
                'title' => 'Purchase Order Cash Approval List',
                'purchaseOrder' => $purchaseOrder,
                'cash_status' => $cash_status,
            ];

            return view('pms.backend.pages.cash-approval.po-cash-approval', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $is_send = ($request->cash_status=='pending' || $request->cash_status=='halt')?'no':'yes';

         $purchaseOrder = PurchaseOrder::whereHas('relQuotation',function ($query){
            $query->where('type','direct-purchase')->where('status','active')
            ->where('is_approved','approved')->where('is_po_generate','yes');
        })
         ->where('id',$request->id)
         ->first();

        DB::beginTransaction();
        try {
            if ($purchaseOrder) {
                $purchaseOrder->update([
                    'is_send'=>$is_send,
                    'cash_status' => $request->cash_status,
                    'cash_note' => $request->cash_note,
                ]); 
                //Notification
                $message = '<span class="notification-links" data-src="'.route('pms.quotation.quotations.cs.proposal.details',$purchaseOrder->id).'" data-ttile="Purchase Order Details">Reference No:'.$purchaseOrder->reference_no.'.Cash '.ucfirst($request->cash_status).' By Accounts.</span>';

                if($request->cash_status=='approved'){

                    CreateOrUpdateNotification('',getManagerInfo('Gate Permission',$purchaseOrder->hr_unit_id),$message,'unread','send-to-gate-manager');
                }
                
                CreateOrUpdateNotification('',getManagerInfo('Purchase-Department'),$message,'unread','send-to-purchase');

                //data commit
                DB::commit();
                return $this->backWithSuccess('Successfully Updated');
            }
            return $this->backWithError('Sorry!! PO Not Found!');

        }catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
