<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Suppliers;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Purchase\PurchaseOrderItem;
use App\Models\PmsModels\Rfp\RequestProposal;
use App\Models\PmsModels\Rfp\RequestProposalDetails;
use App\Models\PmsModels\Rfp\RequestProposalDefineSupplier;
use App\Models\PmsModels\Quotations;
use App\Models\PmsModels\QuotationsItems;
use App\Models\PmsModels\SupplierPaymentTerm;
use App\Models\PmsModels\SupplierPayment;
use App\Models\PmsModels\Requisition;
use App\Models\PmsModels\RequisitionItem;
use App\Models\PmsModels\Purchase\PurchaseOrderRequisition;
use App\Models\Hr\Unit;
use App\Models\Hr\Department;
use App,DB;
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title='Quotations List';
        try {
            $proposals = RequestProposal::with('relQuotations')->paginate(30);
            return view('pms.backend.pages.quotation.index', compact('title','proposals'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function analysisIndex(){
        $title='Quotations Analysis';
        try {
            $quotations=Quotations::where('status','active')->where('is_approved','pending')->groupBy('request_proposal_id')->orderBy('id','desc')->paginate(30);

            return view('pms.backend.pages.quotation.analysis-index', compact('title','quotations'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function quotationItems($quotation_id){
        $title="Quotation wise items";
        $quotations=Quotations::where('id',$quotation_id)->where('status','active')->first();
        try {

            return view('pms.backend.pages.quotation.item-show', compact('quotations','title'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function quotationGenerate($proposal_id){  
        $title = 'Quotation Generate';

        $prefix='QG-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
        $refNo=uniqueCode(14,$prefix,'quotations','id');
        try {

            $supplierPaymentTerms=supplierPaymentTerm();
            $requestProposal=RequestProposal::where('id',$proposal_id)->with('defineToSupplier','requestProposalDetails','requestProposalDetails.product','createdBy')->first();

            $quotationSupplier=Quotations::where('request_proposal_id',$proposal_id)->select('supplier_id')->get();

            $quotationSupplierArray = array();
            foreach($quotationSupplier as $values){
                array_push($quotationSupplierArray,$values->supplier_id);
            }

            return view('pms.backend.pages.quotation.create', compact('title','requestProposal','refNo','supplierPaymentTerms','quotationSupplierArray'));

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

    public function store(Requests\Pms\QuotationRequest $request)
    {   

        //dd($request->all());

        $type=$request->type;
        $modal=Quotations::where([
            'supplier_id'=>$request->supplier_id,
            'request_proposal_id'=>$request->request_proposal_id,
            'type'=>$type
        ])->first();

        if(!empty($modal)){
            return $this->backWithError('Already generate a quotation using this supplier!!');
        }

        DB::beginTransaction();
        try {

            $quotationFilePath='';
            if ($request->hasFile('quotation_file'))
            {
                $quotationFilePath=$this->fileUpload($request->file('quotation_file'),'upload/quotation/pdf-file');
            }

            $quotation=Quotations::create([
                'supplier_id'=>$request->supplier_id,
                'request_proposal_id'=>$request->request_proposal_id,
                'reference_no'=>$request->reference_no,
                'quotation_date'=>date('Y-m-d',strtotime($request->quotation_date)),
                'total_price'=>$request->sum_of_subtoal,
                'discount'=>$request->discount,
                'vat'=>$request->vat==null?0:$request->vat,
                'gross_price'=>$request->gross_price,
                'status'=>'active',
                'type'=>$type,
                'quotation_file'=>$quotationFilePath
            ]);

            foreach ($request->product_id as $i=>$product_id){
                $quotationItemsInput[]=[
                    'quotation_id'=>$quotation->id,
                    'product_id'=>$product_id,
                    'unit_price'=>$request->unit_price[$product_id],
                    'qty'=>$request->qty[$product_id],
                    'sub_total_price'=>$request->sub_total_price[$product_id],
                    'discount'=>$request->item_discount_percent[$product_id]==null?0:$request->item_discount_percent[$product_id],
                    'discount_amount'=>$request->item_discount_amount[$product_id],
                    'vat_percentage'=>$request->product_vat[$product_id],
                    'vat'=>$request->sub_total_vat_price[$product_id],
                    'total_price'=>($request->sub_total_price[$product_id]-$request->item_discount_amount[$product_id])+$request->sub_total_vat_price[$product_id],
                    'created_at'=>date('Y-m-d h:i'),
                ];
            }

            //Quotation items insert.
           QuotationsItems::insert($quotationItemsInput);
           
            if (!is_null($request->payment_term_id)) {
                $this->storeSupplierPaymentTerm($quotation->id, $request);
            }

            DB::commit();
            return $this->backWithSuccess('Quotation Generated Successfully');
        }
        catch (Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }

    public function storeSupplierPaymentTerm($quotationId,$request){
       SupplierPaymentTerm::create(
            [
                'quotation_id'=>$quotationId,
                'supplier_id'=>$request->supplier_id,
                'payment_term_id'=>$request->payment_term_id,
                'payment_percent'=>$request->payment_percent??0,
                'remarks'=>$request->remarks,
            ]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function compareGridView($request_proposal_id)
    {
        try {

            $title='Quotations Compare Analysis';
            $quotations=Quotations::where('status','active')
                        ->where('is_approved','pending')
                        ->where('request_proposal_id',$request_proposal_id)
                        ->orderby('gross_price','asc')
                        ->get();

            return view('pms.backend.pages.quotation._compare2', compact('title','quotations'));

            }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }

    }
    public function compareListView($request_proposal_id)
    {
        try {

            $title='Quotations Compare Analysis';
            $quotations=Quotations::where('status','active')
                        ->where('is_approved','pending')
                        ->where('request_proposal_id',$request_proposal_id)
                        ->orderby('gross_price','asc')
                        ->get();

            return view('pms.backend.pages.quotation._compare', compact('title','quotations'));

            }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }

    }

    public function compareStore(Request $request){
        if(empty($request->quotation_id)){
            return $this->backWithError('Sorry Data Not Found!!');
        }
        DB::beginTransaction();
        try {

            foreach ($request->quotation_id as $key=>$quotation_id){

                $modal=Quotations::where(['id'=>$quotation_id,'request_proposal_id'=>$request->request_proposal_id,'is_approved'=>'pending'])->first();

                if(isset($modal)){

                    $modal->is_approved = 'processing';
                    $modal->note = $request->note[$quotation_id];
                    $modal->supplier_payment_terms_id = $request->supplier_payment_terms_id[$quotation_id];
                    $modal->save();
                }
            }
            
            //Notification
            

            $message= '<span class="notification-links" data-src="'.route('pms.quotation.quotations.cs.proposal.details',$modal->relRequestProposal->id).'" data-ttile="Request Proposal Details">Reference No:'.$modal->reference_no.'.Watting for Approved.</span>';

            CreateOrUpdateNotification('',getManagerInfo('Management'), $message,'unread','send-to-manager','');

            DB::commit();
            return $this->redirectBackWithSuccess('Successfully Send for approval','pms.quotation.quotations.cs.analysis');

        }catch (Throwable $th){

            DB::rollback();
            return $this->redirectBackWithError($th->getMessage(),'pms.quotation.quotations.cs.analysis');
        }
    }
   

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function approvalList()
    {
        $title='Quotations Request For Approved';

        try {

            $quotations = Quotations::where([
                'status' => 'active',
                'is_po_generate' => 'no'
            ])
            ->whereNotIn('is_approved', ['pending','approved'])
            ->orderBy('id','desc')
            ->groupBy('request_proposal_id')
            ->get();


            $quotationList = [];
            foreach ($quotations as $data){

                foreach (Auth::user()->relApprovalRange as $range){
                    //dd($data->relQuotationItems->sum('total_price'));
                    if ($range->min_amount <= $data->relQuotationItems->sum('total_price') && $range->max_amount >= $data->relQuotationItems->sum('total_price')){
                        $quotationList[] = $data;
                    }
                }
            }
            $quotationList = $this->paginate($quotationList, 30);

            //return $quotationList;

            return view('pms.backend.pages.quotation.approval-index', compact('title','quotationList'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function compareView($id,$slug){
        $title='Quotations Compare Analysis';

        try {
            $quotations = Quotations::where(['status'=>'active','request_proposal_id'=>$id])
            ->whereIn('is_approved',['processing','halt'])->orderby('gross_price','asc')->get();

            if($slug=='list'){
                return view('pms.backend.pages.quotation._compare_view_list', compact('title','quotations'));
            }else{
                return view('pms.backend.pages.quotation._compare_view_grid', compact('title','quotations'));
            }
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function approved(Request $request){  

        DB::beginTransaction();
        try {
            $modal= new Quotations();
            $approvedCount = $modal->where('request_proposal_id',$request->request_proposal_id)->where('is_approved','approved')->count();

            if ($approvedCount !=0) {
                return $this->backWithWarning('Already approved One Quotations!!');
            }
            $quotation = $modal->where('request_proposal_id',$request->request_proposal_id)->where('id',$request->quotation_id)->first();

            if(isset($quotation)){

                $quotation->is_approved = 'approved';
                $quotation->remarks = $request->remarks;
                $quotation->save();
            }

            if ($quotation->type=='direct-purchase') {
                $purchaseOrder = $this->directPurchaseStore($quotation);
                if($purchaseOrder){
                    $message = '<span class="notification-links" data-src="'.route('pms.purchase.order-list.show',$purchaseOrder->id).'?view" data-title="Purchase Order Details">Reference No:'.$purchaseOrder->reference_no.'. Request for cash approved.</span>';

                    CreateOrUpdateNotification('',getManagerInfo('Accounts'),$message,'unread','send-to-accounts','');
                }
            }

             $message = '<span class="notification-links" data-src="'.route('pms.quotation.quotations.cs.proposal.details',$request->request_proposal_id).'" data-ttile="Request Proposal Details">Reference No:'.$modal->reference_no.'. Approved By Management.</span>';
            
             CreateOrUpdateNotification('',getManagerInfo('Purchase-Department'),$message,'unread','sent-to-purchase','');

            DB::commit();
            return $this->redirectBackWithSuccess('Successfully approval!!','pms.quotation.approval.list');
        }catch (Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
        return $this->backWithError('Sorry Data Not Found!!');
    }

    public function directPurchaseStore($quotation)
    {   

        $requisition=isset($quotation->relRequestProposal->requestProposalRequisition[0])?$quotation->relRequestProposal->requestProposalRequisition[0]->relRequisition:false;

        if($requisition){

        $prefix='PO-'.date('y', strtotime(date('Y-m-d'))).'-'.unitName($requisition->hr_unit_id)->hr_unit_short_name.'-';
        $refNo=uniqueCode(16,$prefix,'purchase_orders','id');

            $po_data = new PurchaseOrder();
            $po_data->quotation_id = $quotation->id;
            $po_data->hr_unit_id = $requisition->hr_unit_id;
            $po_data->reference_no = $refNo;
            $po_data->po_date = date('Y-m-d h:i:s');
            $po_data->remarks = $quotation->remarks;
            $po_data->save();

            $poSubTotal=0;
            $poDiscount=0;
            $poVat=0;
            $poGrossTotal=0;

            $collectProductId=[];
            foreach($quotation->relQuotationItems as $key=> $values){

                $poQty=$values->qty;
                $subTotal=$values->unit_price*$poQty;
                $poSubTotal +=$subTotal;
                $discountAmount=($values->discount*($values->unit_price*$poQty))/100;
                $poDiscount +=$discountAmount;
                $vatAmount=($values->vat_percentage*($values->unit_price*$poQty))/100;
                $poVat +=$vatAmount;
                $grossTotal=($subTotal+$vatAmount)-$discountAmount;
                $poGrossTotal +=$grossTotal;

                $po_items = new PurchaseOrderItem();
                $po_items->po_id=$po_data->id; 
                $po_items->product_id=$values->product_id; 
                $po_items->unit_price=$values->unit_price; 
                $po_items->qty=$poQty;
                $po_items->sub_total_price=$subTotal;
                $po_items->discount_percentage=$values->discount;
                $po_items->discount=$discountAmount;
                $po_items->vat_percentage=$values->vat_percentage;
                $po_items->vat=$vatAmount;
                $po_items->total_price = $grossTotal;
                $po_items->save();

                array_push($collectProductId,$values->product_id);
            }

            //Update Purcahse Order
            $po_data->update([
                'total_price' => $poSubTotal,
                'discount' => $poDiscount,
                'vat' => $poVat,
                'gross_price' => $poGrossTotal,
            ]);
           
            //Add Supplier Pyaments
            $duration_date= $quotation->relSupplierPaymentTerm->day_duration;
            $pay_date=date('Y-m-d h:i:s', strtotime('+'.$duration_date.' day', strtotime($po_data->po_date)));
            //Payment date based on advance & due
            $supplier_payment = new SupplierPayment();
            $supplier_payment->supplier_id = $quotation->supplier_id;
            $supplier_payment->purchase_order_id = $po_data->id;
            $supplier_payment->transection_date = date('Y-m-d h:i:s');
            $supplier_payment->transection_type = 'purchase';
            $supplier_payment->pay_amount = ($quotation->relSupplierPaymentTerm->payment_percent * $quotation->gross_price)/100;
            $supplier_payment->pay_date = $pay_date;
            $supplier_payment->bill_type = 'po-advance';
            $supplier_payment->save();
            //Update requisition
            $requisition->items()->whereIn('product_id', $collectProductId)->where('is_send','yes')
            ->where('po_generate','no')
            ->update(['po_generate'=>'yes']);
            //update quotation
            $quotation->update(['is_po_generate'=>'yes']);

            PurchaseOrderRequisition::updateOrCreate([
                'purchase_order_id' => $po_data->id,
                'requisition_id' => $requisition->id,
            ],[
                'hr_department_id' => $requisition->relUsersList->employee->as_department_id,
            ]);
            
            return $po_data;
        }

        return false;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleQuotationStatus(Request $request){
        $quotation = Quotations::where('id',$request->id)->first();

        if(isset($quotation->id)){
            $newStatus = $request->status;
            $newText = $newStatus == 'approved' ? 'Approved' : (($newStatus == 'halt')? 'Halt' : 'Pending');
            $update = $quotation->update(['is_approved' => $newStatus,'updated_at' => date('Y-m-d H:i:s'),'updated_by' => Auth::user()->id]);
            if($update){
                return response()->json([
                    'success' => true,
                    'new_text' => $newText,
                    'message' => 'Data has been updated!'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Something Went Wrong!'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Data not found!'
        ]);
    }

    public function haltStatus(Request $request){
        $quotation = Quotations::findOrFail($request->id);
        try{
            $quotation->update([
                'remarks' => $request->remarks,
                'is_approved' => 'halt',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id
            ]);
            return $this->backWithSuccess('Quotation Successfully Halt!!');
        }catch (\Throwable $th){

            return $this->backWithError($th->getMessage());
        }
    }


    public function search(Request $request)
    {
        $response = [];

        $from_date=date('Y-m-d',strtotime($request->from_date));
        $to_date=date('Y-m-d',strtotime($request->to_date));

        $is_approved=$request->is_approved;
        $is_po_generate=$request->is_po_generate;

        $datas = Quotations::whereDate('quotation_date', '>=', $from_date)
            ->whereDate('quotation_date', '<=', $to_date)
            ->when($is_approved, function($query) use($is_approved){
                return $query->where('is_approved',$is_approved);
            })
            ->when($is_po_generate, function($query) use($is_po_generate){
                return $query->where('is_po_generate',$is_po_generate);
            })
            ->where('status','active')
            ->where('type','!=','direct-purchase')
            ->paginate(100);

        $quotationList = [];
        foreach ($datas as $data){
            foreach (Auth::user()->relApprovalRange as $range){
                if ($range->min_amount <= $data->relQuotationItems->sum('total_price') && $range->max_amount >= $data->relQuotationItems->sum('total_price')){
                    $quotationList[] = $data;
                }
            }
        }
        $quotationList = $this->paginate($quotationList, 100);

        try {
            if(count($quotationList)>0)
            {
                $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.quotation._quotation-list-search',
                    ['quotationList'=> $quotationList]);
                $contents = $body->render();

                $response['result'] = 'success';
                $response['body'] = $contents;
            }else{

                $response['result'] = 'error';
                $response['message'] = 'Data not found.!!';
            }

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }

        return $response;
    }


    public function generatePoList()
    {
        $title='Quotations Approved List';

        try {

            $quotationList=Quotations::where(['status'=>'active', 'is_approved'=>'approved', 'is_po_generate'=>'no'])
            ->orderBy('id','desc')
            ->paginate(30);

            return view('pms.backend.pages.quotation.generate-po-list', compact('title','quotationList'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function generatePoProcess($id)
    {
       $title='Generate Purchase Order';
       $quotation = Quotations::where(['status'=>'active', 'is_approved'=>'approved', 'is_po_generate'=>'no'])->findOrFail($id);

       try{

        $units = Unit::pluck('hr_unit_short_name','hr_unit_id')->all();
        $departments = Department::pluck('hr_department_name','hr_department_id')->all();

        return view('pms.backend.pages.quotation.generate-po-process',compact('title','quotation','units', 'departments'));

       }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
       }
    }

    public function unitWiseRequisition($unitId,$quotationId)
    {
        try{

            $productIds = QuotationsItems::where('quotation_id', $quotationId)
            ->pluck('product_id')->toArray();

            $array1 = Requisition::where([
                        'hr_unit_id' => $unitId,
                        'is_po_generate' => 'no',
                        'is_send_to_rfp' => 'yes',
                        'delivery_status' => 'rfp',
                        'approved_id' => 1,
                        'status' => 1,
                    ]) 
                    ->whereHas('requisitionItems', function($query) use($productIds) {
                        return $query->where('is_send','yes')
                                      ->where('po_generate','no')
                                      ->whereIn('product_id',$productIds);
                    })
                    ->whereHas('requestProposalRequisition.relRequestProposal.relQuotations', function($query) use($quotationId){
                        return $query->where('id', $quotationId);
                    })
                    ->whereHas('relUsersList.employee', function($query){
                        return $query->where('as_department_id', request()->get('hr_department_id'));
                    })
                    ->pluck('id')->toArray();

            $array2 = Requisition::where([
                        'hr_unit_id' =>$unitId,
                        'approved_id' => 1,
                        'status' => 1,
                        'is_po_generate' => 'no',
                        'is_send_to_rfp' => 'yes',
                        'request_status' => 'send_rfp',
                        'delivery_status' => 'partial-delivered',
                    ]) 
                    ->whereHas('requisitionItems', function($query) use($productIds) {
                        return $query->where('is_send','yes')
                                      ->where('po_generate','no')
                                      ->whereIn('product_id',$productIds);
                    })
                    ->whereHas('requestProposalRequisition.relRequestProposal.relQuotations', function($query) use($quotationId){
                        return $query->where('id', $quotationId);
                    })
                    ->whereHas('relUsersList.employee', function($query){
                        return $query->where('as_department_id', request()->get('hr_department_id'));
                    })
                    ->pluck('id')->toArray();
                    
            $array = array_unique(array_merge($array1, $array2));

            return Requisition::whereIn('id',$array)->get(['id','reference_no']);

        }catch(\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function requisitionWiseItemsQty(Request $request)
    {
        try{

            $items = QuotationsItems::where('quotation_id', $request->quotationId)->get();
            $data = [];
            if(isset($items[0])){
                foreach($items as $key => $item){
                    $qty = RequisitionItem::whereIn('requisition_id',$request->requisitionId)
                                        ->where('product_id', $item->product_id)
                                        ->where('is_send','yes')
                                        ->where('po_generate','no')
                                        ->sum('qty');

                    $deliveryQty = RequisitionItem::whereIn('requisition_id',$request->requisitionId)
                        ->where('product_id', $item->product_id)
                        ->where('is_send','yes')
                        ->where('po_generate','no')
                        ->sum('delivery_qty');

                    $data[$item->id] = ($deliveryQty>0)?$qty-$deliveryQty:$qty;
                }
            }

            return $data;
        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function generatePoStore(Request $request){   
        
        $this->validate($request, [
            'quotation_id' => ['required'],
            'po_qty' => ['required'],
            'requisition_id' => ['required'],
            'hr_unit_id' => ['required'],
            'hr_department_id' => ['required'],
        ]);

        $filterPoQty = array_diff($request->po_qty, [0]);
        $collectProductId=array_keys($filterPoQty);
        if(array_sum($filterPoQty)<=0){
            return $this->backWithError('Please po qty can not be 0');
        }

        $modal = Quotations::where('id',$request->quotation_id)->first();

        $prefix='PO-'.date('y', strtotime(date('Y-m-d'))).'-'.unitName($request->hr_unit_id)->hr_unit_short_name.'-';
        $refNo=uniqueCode(16,$prefix,'purchase_orders','id');

      
        DB::beginTransaction();
        try{
            $po_data = new PurchaseOrder();
            $po_data->quotation_id = $modal->id;
            $po_data->hr_unit_id = $request->hr_unit_id;
            $po_data->reference_no = $refNo;
            $po_data->po_date = date('Y-m-d',strtotime($request->po_date));
            $po_data->remarks = $request->remarks;
            $po_data->save();

            $poSubTotal=0;
            $poDiscount=0;
            $poVat=0;
            $poGrossTotal=0;

            $items = QuotationsItems::where('quotation_id',$modal->id )->whereIn('product_id',$collectProductId)->get();
            foreach($items as $key=> $values){

                $poQty=$filterPoQty[$values->product_id];
                $subTotal=$values->unit_price*$poQty;
                $poSubTotal +=$subTotal;
                $discountAmount=($values->discount*($values->unit_price*$poQty))/100;
                $poDiscount +=$discountAmount;
                $vatAmount=($values->vat_percentage*($values->unit_price*$poQty))/100;
                $poVat +=$vatAmount;
                $grossTotal=($subTotal+$vatAmount)-$discountAmount;
                $poGrossTotal +=$grossTotal;

                $po_items = new PurchaseOrderItem();
                $po_items->po_id=$po_data->id; 
                $po_items->product_id=$values->product_id; 
                $po_items->unit_price=$values->unit_price; 
                $po_items->qty=$poQty;
                $po_items->sub_total_price=$subTotal;
                $po_items->discount_percentage=$values->discount;
                $po_items->discount=$discountAmount;
                $po_items->vat_percentage=$values->vat_percentage;
                $po_items->vat=$vatAmount;
                $po_items->total_price = $grossTotal;
                $po_items->save();
            }

            //Update Purcahse Order
            PurchaseOrder::where('id',$po_data->id)->update([
                'total_price' => $poSubTotal,
                'discount' => $poDiscount,
                'vat' => $poVat,
                'gross_price' => $poGrossTotal,
            ]);
            //$modal->relRequestProposal->relQuotations->each->update(['is_po_generate'=>'yes']);
            //Add Supplier Pyaments
            $duration_date= $modal->relSupplierPaymentTerm->day_duration;
            $pay_date=date('Y-m-d h:i:s', strtotime('+'.$duration_date.' day', strtotime($po_data->po_date)));
            //Payment date based on advance & due
            $supplier_payment = new SupplierPayment();
            $supplier_payment->supplier_id = $modal->supplier_id;
            $supplier_payment->purchase_order_id = $po_data->id;
            $supplier_payment->transection_date = date('Y-m-d h:i:s');
            $supplier_payment->transection_type = 'purchase';
            $supplier_payment->pay_amount = ($modal->relSupplierPaymentTerm->payment_percent * $modal->gross_price)/100;
            $supplier_payment->pay_date = $pay_date;
            $supplier_payment->bill_type = 'po-advance';
            $supplier_payment->save();
            //End Supplier Payments
            //Update requisition

            //Requisition::whereIn('id',$request->requisition_id)->update(['is_po_generate'=>'yes']);

            RequisitionItem::whereIn('requisition_id', $request->requisition_id)
                            ->whereIn('product_id', $collectProductId)
                            ->where('is_send','yes')
                            ->where('po_generate','no')
                            ->update(['po_generate'=>'yes']);

            if(isset($request->requisition_id[0])){
                foreach($request->requisition_id as $key => $requisition_id){
                    PurchaseOrderRequisition::updateOrCreate([
                        'purchase_order_id' => $po_data->id,
                        'requisition_id' => $requisition_id,
                    ],[
                        'hr_department_id' => $request->hr_department_id,
                    ]);
                }
            }

            DB::commit();
            return $this->backWithSuccess('Successfully quotation generated!!');
            //Request Proposal Tracking
            //\App\Models\PmsModels\RequestProposalTracking::StoreRequestProposalTracking($modal->request_proposal_id,'PO-Generate');
        }catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }

        return back();
    }

    public function completeQuotation(Request $request)
    {
        $response=[];
        $data=Quotations::where('id',$request->quotation_id)->first();
        //Start transaction
        DB::beginTransaction();
        try {
            if(!empty($data))
            {
                $data->is_po_generate = 'yes';
                $data->save();
                //Commit data
                DB::commit();

                $response['result'] = 'success';
                $response['message'] = 'Successfully Complete This Quotation!!';
            }else{
                $response['result'] = 'error';
                $response['message'] = 'Data not found.!!';
            }

        }catch (\Throwable $th){
            //If process has any problem then rollback the data
            DB::rollback();
            $response['result'] = 'error';
            $response['message'] = $th->getMessage();
        }
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function proposalDetailsView($id)
    {
        $title='Requests Proposal Details';

        try {

             $requestProposal=RequestProposal::where('id',$id)->with('defineToSupplier','requestProposalDetails','requestProposalDetails.product','createdBy')->first();

            return view('pms.backend.pages.rfp.request-proposal-details', compact('title','requestProposal'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
}
