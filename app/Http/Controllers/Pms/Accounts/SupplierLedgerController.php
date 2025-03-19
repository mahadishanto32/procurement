<?php

namespace App\Http\Controllers\Pms\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PmsModels\SupplierPayment;
use App\Models\PmsModels\SupplierLedgers;
use App\Models\PmsModels\Suppliers;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use DB;
use Auth;
use App;

class SupplierLedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $from_date = request()->has('from_date') && !empty(request()->get('from_date')) ? date("Y-m-d", strtotime(request()->get('from_date'))) : date("Y-m-d", strtotime(date('Y-m-01')));
            $to_date = request()->has('to_date') && !empty(request()->get('to_date')) ? date("Y-m-d", strtotime(request()->get('to_date'))) : date("Y-m-d", strtotime(date('Y-m-t')));
            $supplier_id = request()->has('supplier_id') ? request()->get('supplier_id') : null;

            $suppliers = Suppliers::where('status', 'Active')->has('relPayments.relSupplierLedgers')->get(['id', 'name']);

            $chooseSuppliers = $suppliers;
            $ledgers = \App\Models\PmsModels\SupplierLedgers::whereHas('relSupplierPayment', function($query) use($supplier_id){
                return $query->where('supplier_id', $supplier_id);
            })
            ->whereBetween('date', [$from_date, $to_date])
            ->paginate(30);

            $data = [
                'title' => 'Supplier Ledger',
                'from_date' => $from_date,
                'to_date' => $to_date,
                'supplier_id' => $supplier_id,
                'chooseSuppliers' => $chooseSuppliers,
                'ledgers' => $ledgers
            ];

            return view('pms.backend.pages.accounts.supplier-ledger-list', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
}
