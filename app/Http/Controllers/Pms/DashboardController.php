<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use Cache, DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\PmsModels\Requisition;
use App\Models\PmsModels\Notification;
use App\Models\PmsModels\RequisitionDeliveryItem;
use App\Models\PmsModels\Grn\GoodsReceivedItemStockIn;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Rfp\RequestProposal;
use App\Models\PmsModels\Quotations;
use App\Models\PmsModels\QuotationsItems;
use Auth;

class DashboardController extends Controller
{  
    public function index()
    {    
        $title='PMS Dashboard';

        $proposals = RequestProposal::with('relQuotations')->count();
        $quotationList=Quotations::where(['status'=>'active', 'is_approved'=>'approved', 'is_po_generate'=>'yes'])->count();

        $purchaseOrderList = PurchaseOrder::count();

        $quotationsData=Quotations::where(['status'=>'active','is_po_generate'=>'no'])
            ->whereNotIn('is_approved',['pending','approved'])
            ->orderBy('id','desc')
            ->groupBy('request_proposal_id')
            ->get();

        $quotationListArray = [];
        foreach ($quotationsData as $data){
            foreach (Auth::user()->relApprovalRange as $range){
                if ($range->min_amount <= $data->relQuotationItems->sum('total_price') && $range->max_amount >= $data->relQuotationItems->sum('total_price')){
                    $quotationListArray[] = $data;
                }
            }
        }
        $quotationListCount = count($quotationListArray);

        return view('pms.backend.pages.dashboard',compact('title','proposals','quotationList','purchaseOrderList','quotationListCount'));
    }
}
