<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hr\Leave;

use App\Models\PmsModels\Requisition;
use App\Models\PmsModels\RequisitionTracking;
use App\Models\PmsModels\Notification;
use App\Models\PmsModels\RequisitionDeliveryItem;
use App\Models\PmsModels\Grn\GoodsReceivedItemStockIn;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Rfp\RequestProposal;
use App\Models\PmsModels\Quotations;
use App\Models\PmsModels\QuotationsItems;

use Illuminate\Support\Facades\Artisan;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //  $att = $this->userAtt();
        //  $associate_id = auth()->user()->associate_id;
        //  $leaves = array();
        //  if($associate_id){
        //      $leaves= Leave::where('leave_ass_id', $associate_id)
        //                  ->whereYear('leave_to', date('Y'))
        //                  ->orderBy('id', 'DESC')
        //                  ->take(5)
        //                  ->get();

        // }

        //  return view('user.index', compact('att','leaves'));


        // $purchaseOrder=\App\Models\PmsModels\Purchase\PurchaseOrder::findOrFail(1);
        // $data = [];
        // $data["email"] = 'anwarullah834@gmail.com';
        // $data["title"] = "Purchase Order Email";
        // $data["reference_no"] = $purchaseOrder->reference_no;
        // $data["purchaseOrder"] = $purchaseOrder;

        // $pdf = outputMPDF('pms.backend.pages.billing.po-invoice-pdf', $data, $data["reference_no"], $data["reference_no"]);

        // Mail::send('pms.backend.mail.po_mail_body', $data, function ($message) use ($data, $pdf) {
        //     $message->to($data["email"], $data["email"])
        //     ->subject($data["title"])
        //     ->attachData($pdf, $data["reference_no"].".pdf");
        // });

        // $tables = [
        //     'billing_chalan',
        //     'goods_received_items',
        //     'goods_received_items_stock_in',
        //     'goods_received_notes',
        //     'inventory_details',
        //     'inventory_logs',
        //     'inventory_summaries',
        //     'notifications',
        //     'purchase_orders',
        //     'purchase_order_attachment',
        //     'purchase_order_items',
        //     'purchase_order_requisitions',
        //     'purchase_returns',
        //     'quotations',
        //     'quotations_items',
        //     'request_proposals',
        //     'request_proposal_define_suppliers',
        //     'request_proposal_details',
        //     'request_proposal_requisitions',
        //     'request_proposal_tracking',
        //     'requisitions',
        //     'requisition_deliveries',
        //     'requisition_delivery_items',
        //     'requisition_items',
        //     'requisition_note_logs',
        //     'requisition_tracking',
        //     'requisition_types',
        //     'return_change_faq',
        //     'supplier_ledgers',
        //     'supplier_payments',
        //     'supplier_rattings'
        // ];

        // foreach($tables as $table){
        //     DB::table($table)->where('id', '!=', '')->delete();
        // }

        $data = [
            'title' => 'PMS Dashboard',
        ];
        return view('pms.backend.pages.dashboard', $data);
    }


    public function userAtt()
    {
        $user = auth()->user();
        if($user->employee){
            
            $table = get_att_table($user->employee['as_unit_id']);
            $as_id = $user->employee['as_id'];


            $present  = DB::table($table)
                        ->whereMonth('in_date', date('m'))
                        ->whereYear('in_date',date('Y'))
                        ->where('as_id', $as_id)
                        ->count();

            $late  = DB::table($table)
                        ->whereMonth('in_date', date('m'))
                        ->whereYear('in_date',date('Y'))
                        ->where('as_id', $as_id)
                        ->where('late_status', 1)
                        ->count();
          
            /*----------------Leave------------------*/
            $leave = DB::table('hr_leave')
                     ->where('leave_status', '=', 1)
                     ->count();

            $absent = DB::table('hr_absent')
                       ->whereMonth('date', date('m'))
                       ->where('associate_id', $user->associate_id)
                       ->count();
        }

        $chartdata=[
            'present' => $present??0,
            'late' => $late??0,
            'leave' => $leave??0,
            'absent' => $absent??0
        ];

        return $chartdata;
    }
    
    public function login()
    {
        return view('login');
    }


    public function clear()
    {
        $exitCode = Artisan::call('config:clear');
        $exitCode = Artisan::call('cache:clear');
        $exitCode = Artisan::call('route:clear');
        $exitCode = Artisan::call('config:cache');
        // $exitCode = Artisan::call('route:cache');
        // $exitCode = Artisan::call('clear-compiled');
        // $exitCode = Artisan::call('optimize');
        return 'DONE'; //Return anything
    }
}
