<?php

namespace App\Http\Controllers\Pms\Purchase;

use App\Http\Controllers\Controller;
use App\Mail\Pms\PurchaseOrderMail;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Purchase\PurchaseOrderItem;
use App\Models\PmsModels\Requisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use DB;
use PDF;

class PurchaseController extends Controller
{

    public function orderIndex()
    {
        try {
            
            $title = 'Purchase Order';
            $purchaseOrderList= PurchaseOrder::whereHas('relQuotation', function($query){
                return $query->whereNotIn('type',['direct-purchase']);
            })->paginate(100);

            return view('pms.backend.pages.purchase.order-list', compact('title', 'purchaseOrderList'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function sendMailToSupplier($id)
    {   
        DB::beginTransaction();
        try {
            $purchaseOrder=PurchaseOrder::findOrFail($id);
            $purchaseOrder->is_send = 'yes';
            $purchaseOrder->save();


            $data["email"] = $purchaseOrder->relQuotation->relSuppliers->email;
            $data["title"] = "Purchase Order Email";
            $data["reference_no"] = $purchaseOrder->reference_no;
            $data["purchaseOrder"] = $purchaseOrder;
            
            // $pdf = PDF::loadView('pms.backend.mail.purchase-order-mail', $data)->setOptions(['defaultFont' => 'sans-serif']);


            // Mail::send('pms.backend.mail.po_mail_body', $data, function ($message) use ($data, $pdf) {
            //     $message->to($data["email"], $data["email"])
            //     ->subject($data["title"])
            //     ->attachData($pdf->output(), $data["reference_no"].".pdf");
            // });


            // $pdf = outputMPDF('pms.backend.pages.billing.po-invoice-pdf', $data, $data["reference_no"], $data["reference_no"]);

            // Mail::send('pms.backend.mail.po_mail_body', $data, function ($message) use ($data, $pdf) {
            //     $message->to($data["email"], $data["email"])
            //     ->subject($data["title"])
            //     ->attachData($pdf, $data["reference_no"].".pdf");
            // });

            //Notification
            $message = '<span class="notification-links" data-src="'.route('pms.quotation.quotations.cs.proposal.details',$purchaseOrder->id).'" data-ttile="Purchase Order Details">Reference No:'.$purchaseOrder->reference_no.'. Approved By Management.</span>';

            CreateOrUpdateNotification('',getManagerInfo('Gate Permission',$purchaseOrder->hr_unit_id),$message,'unread','send-to-gate-manager');

            DB::commit();

            return $this->backWithSuccess('Successfully PO send to supplier!!');

        }catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }

    public function show($id)
    {
        $response=[];

        try{
           $modal = PurchaseOrder::with('relQuotation','relQuotation.relSuppliers')->findOrFail($id);
           if ($modal) {
            $body = View::make('pms.backend.pages.purchase.show',
                ['purchaseOrder'=> $modal]);
            if (request()->has('view')) {
                return $body->render();
            }
            $response['result'] = 'success';
            $response['body'] = $body->render();
            $response['message'] = 'Successfully Generated PO';
        }else{
         $response['result'] = 'error';
         $response['message'] = 'Purchase Order not found!!';
     }

 }catch(\Throwable $th){
    $response['result'] = 'error';
    $response['message'] = $th->getMessage();
}

return $response;
}
}
