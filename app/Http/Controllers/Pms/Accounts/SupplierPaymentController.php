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

class SupplierPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $supplier_id = request()->has('supplier_id') ? request()->get('supplier_id') : null;

            $suppliers = Suppliers::where('status', 'Active')->has('relPayments')->get(['id', 'name']);

            $purchase_order = PurchaseOrder::where('is_send','yes')->whereHas('relSupplierPayments',function ($query){
                $query->whereRaw('purchase_orders.id=supplier_payments.purchase_order_id');
            })
            ->when(!empty($supplier_id), function($query) use($supplier_id){
                return $query->whereHas('relQuotation', function($query) use($supplier_id){
                    return $query->where('supplier_id', $supplier_id);
                });
            })
            ->paginate(100);

            $data = [
                'title' => 'Supplier Payments',
                'supplier_id' => $supplier_id,
                'suppliers' => $suppliers,
                'purchase_order' => $purchase_order,
            ];

            return view('pms.backend.pages.accounts.supplier-payment-list', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function billingList()
    {
        try{

            $title="Billing List";

            $purchase_order =PurchaseOrder::with('relPurchaseOrderItems','relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems','relPoAttachment')
            ->where('is_send','yes')
            ->whereHas('relGoodReceiveNote',function ($query){
                $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
            })
            ->whereHas('relPoAttachment',function ($query){
                $query->where('status','approved');
            })
            ->paginate(100);

            if (count($purchase_order)>0){
                calculateGrnQtyAgainstPurchaseOrder($purchase_order);
            }

            return view('pms.backend.pages.accounts.billing-list',compact('title','purchase_order'));
        }catch(\Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }
    }

    public function poInvoiceList($id)
    {
        try{

            $title="Purchase Order Wise Challan List";

            $purchaseOrder=PurchaseOrder::findOrFail($id);
            $goodsReceivedItem=GoodsReceivedItem::query();
            $poAttachment=PurchaseOrderAttachment::query();

            return view('pms.backend.pages.accounts.po-invoice-list',compact('title','purchaseOrder','goodsReceivedItem','poAttachment'));
        }catch(\Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }
    }

    /**
     * Display the specified resource.Purchase order wise ledger generate
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // public function ledgerPOWiseGenerate(Request $request)
    // {
    //     $payments= SupplierPayment::where([
    //         'purchase_order_id' => $request->id,
    //         'status' => 'audited',
    //         ['bill_amount', '>', 0]
    //     ])->get();

    //     DB::beginTransaction();
    //     try{
    //         if(isset($payments[0])){
    //             foreach($payments as $key => $payment){
    //                 $supplierOpeningBalance = supplierOpeningBalance($payment->supplier_id);

    //                 SupplierLedgers::updateOrCreate([
    //                     'supplier_payment_id' => $payment->id
    //                 ], [
    //                     'date' => date('Y-m-d'),
    //                     'opening_balance' => $supplierOpeningBalance['balance'],
    //                     'debit' => $payment->bill_amount,
    //                     'credit' =>  $payment->pay_amount,
    //                     'closing_balance' => ($supplierOpeningBalance['balance']+$payment->bill_amount)-$payment->pay_amount
    //                 ]);

    //                 $payment->status = 'approved';
    //                 $payment->save();
    //             }

    //             DB::commit();
    //             return response()->json([
    //                 'success' => true,
    //                 'new_text' => "Ledger Generated",
    //                 'message' => "Ledger Generate Successfully"
    //             ]);
    //         }

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Data not found!'
    //         ]);
    //     }catch(\Throwable $th){
    //         DB::rollback();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $th->getMessage()
    //         ]);
    //     }
    // }

    // public function ledgerGenerate(Request $request)
    // {
    //     $supplier_payment= SupplierPayment::findOrFail($request->id);
    //     DB::beginTransaction();
    //     try{

    //         if (isset($supplier_payment->id)) {
    //             $supplierOpeningBalance = supplierOpeningBalance($supplier_payment->supplier_id);

    //             SupplierLedgers::create([
    //                 'supplier_payment_id' => $supplier_payment->id,
    //                 'date' => date('Y-m-d'),
    //                 'opening_balance' => $supplierOpeningBalance['balance'],
    //                 'debit' => $supplier_payment->bill_amount,
    //                 'credit' =>  $supplier_payment->pay_amount,
    //                 'closing_balance' => ($supplierOpeningBalance['balance']+$supplier_payment->bill_amount)-$supplier_payment->pay_amount
    //             ]);

    //             $supplier_payment->status = 'approved';
    //             $supplier_payment->save();

    //               DB::commit();

    //             return response()->json([
    //                 'success' => true,
    //                 'new_text' => "Ledger Generated",
    //                 'message' => "Ledger Generate Successfully"
    //             ]);
    //         }

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Data not found!'
    //         ]);
            
            
    //     }catch(\Throwable $th){
    //          DB::rollback();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $th->getMessage()
    //         ]);
    //     }
    // }

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
        $request->validate([
            'pay_amount.*' => 'required',
        ]);

        DB::beginTransaction();
        try{
            $advancePayments = SupplierPayment::whereIn('id', isset($request->due_amount) && is_array($request->due_amount) ? array_keys($request->due_amount) : [])->doesntHave('relSupplierLedgers')->get();
            if(isset($advancePayments[0])){
                foreach($advancePayments as $key => $payment){
                    $supplierOpeningBalance = supplierOpeningBalance($payment->supplier_id);
                    SupplierLedgers::create([
                        'supplier_payment_id' => $payment->id,
                        'date' => date('Y-m-d'),
                        'opening_balance' => $supplierOpeningBalance['balance'],
                        'debit' => 0,
                        'credit' =>  $payment->pay_amount,
                        'closing_balance' => $supplierOpeningBalance['balance']-$payment->pay_amount
                    ]);
                }
            }

            foreach($request->pay_amount as $payment_id => $pay_amount){
                $payment = SupplierPayment::find($payment_id);
                $due_amount = $payment->bill_amount-$payment->pay_amount;

                if($pay_amount <= $due_amount){
                    $payment->pay_amount = $payment->pay_amount+$pay_amount;
                    $payment->save();

                    $supplierOpeningBalance = supplierOpeningBalance($payment->supplier_id);
                    SupplierLedgers::create([
                        'supplier_payment_id' => $payment->id,
                        'date' => date('Y-m-d'),
                        'opening_balance' => $supplierOpeningBalance['balance'],
                        'debit' => 0,
                        'credit' =>  $payment->pay_amount,
                        'closing_balance' => $supplierOpeningBalance['balance']-$payment->pay_amount
                    ]);
                }
            }

            DB::commit();
            return $this->backWithSuccess('Payment has been proessed Successfully.');
        }catch(\Throwable $th){
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
