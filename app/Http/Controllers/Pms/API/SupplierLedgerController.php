<?php

namespace App\Http\Controllers\Pms\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PmsModels\SupplierPayment;
use App\Models\PmsModels\SupplierLedgers;
use App\Models\PmsModels\Suppliers;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use DB;
use App;

class SupplierLedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function supplierLedgers()
    {
        try {
            $from_date = request()->has('from_date') && !empty(request()->get('from_date')) ? date("Y-m-d", strtotime(request()->get('from_date'))) : date("Y-m-d", strtotime(date('Y-m-01')));
            $to_date = request()->has('to_date') && !empty(request()->get('to_date')) ? date("Y-m-d", strtotime(request()->get('to_date'))) : date("Y-m-d", strtotime(date('Y-m-t')));
            
            $ledgers = \App\Models\PmsModels\SupplierLedgers::with(['relSupplierPayment', 'relSupplierPayment.relSupplier', 'relSupplierPayment.relPurchaseOrder'])
            ->whereBetween('date', [$from_date, $to_date])->get()->toArray();

            $ledgers = array_map(function($ledger){
            return [
                'ledger_id' => $ledger['id'],
                'date' => $ledger['date'],
                'opening_balance' => $ledger['opening_balance'],
                'debit' => $ledger['debit'],
                'credit' => $ledger['credit'],
                'closing_balance' => $ledger['closing_balance'],
                
                'supplier_payment_id' => $ledger['supplier_payment_id'],
                "transaction_type" =>  $ledger['rel_supplier_payment']['transection_type'],
                "bill_amount" =>  $ledger['rel_supplier_payment']['bill_amount'],
                "pay_amount" =>  $ledger['rel_supplier_payment']['pay_amount'],
                "bill_type" =>  $ledger['rel_supplier_payment']['bill_type'],
                "transaction_date" =>  $ledger['rel_supplier_payment']['transection_date'],
                "pay_date" =>  $ledger['rel_supplier_payment']['pay_date'],

                "purchase_order_id" => $ledger['rel_supplier_payment']['purchase_order_id'],
                "reference_no" => $ledger['rel_supplier_payment']['rel_purchase_order']['reference_no'],
                "po_date" => $ledger['rel_supplier_payment']['rel_purchase_order']['po_date'],
                "total_price" => $ledger['rel_supplier_payment']['rel_purchase_order']['total_price'],
                "discount_percentage" => $ledger['rel_supplier_payment']['rel_purchase_order']['discount_percentage'],
                "discount" => $ledger['rel_supplier_payment']['rel_purchase_order']['discount'],
                "vat_percentage" => $ledger['rel_supplier_payment']['rel_purchase_order']['vat_percentage'],
                "vat" => $ledger['rel_supplier_payment']['rel_purchase_order']['vat'],
                "gross_price" => $ledger['rel_supplier_payment']['rel_purchase_order']['gross_price'],
                "remarks" => $ledger['rel_supplier_payment']['rel_purchase_order']['remarks'],

                'supplier_id' => $ledger['rel_supplier_payment']['supplier_id'],
                "supplier_name" => $ledger['rel_supplier_payment']['rel_supplier']['name'],
                "supplier_email" => $ledger['rel_supplier_payment']['rel_supplier']['email'],
                "supplier_phone" => $ledger['rel_supplier_payment']['rel_supplier']['phone'],
                "supplier_mobile_no" => $ledger['rel_supplier_payment']['rel_supplier']['mobile_no'],
                "supplier_trade" => $ledger['rel_supplier_payment']['rel_supplier']['trade'],
                "supplier_owner_name" => $ledger['rel_supplier_payment']['rel_supplier']['owner_name'],
                "supplier_owner_nid" => $ledger['rel_supplier_payment']['rel_supplier']['owner_nid'],
                "supplier_owner_email" => $ledger['rel_supplier_payment']['rel_supplier']['owner_email'],
                "supplier_owner_contact_no" => $ledger['rel_supplier_payment']['rel_supplier']['owner_contact_no'],
                "supplier_status" => $ledger['rel_supplier_payment']['rel_supplier']['status'],
            ];
           }, $ledgers);

            return response()->json([
                'ledgers' => $ledgers
            ], 200);
        }catch (\Throwable $th){
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
