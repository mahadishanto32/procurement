<?php

namespace App\Http\Controllers\Pms;
use App\Http\Controllers\Controller;
use App\Mail\Pms\RequestForProposalToSupplierMail;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Requisition;
use App\Models\PmsModels\RequisitionItem;
use App\Models\PmsModels\RequisitionType;
use App\Models\PmsModels\Rfp\RequestProposal;
use App\Models\PmsModels\Rfp\RequestProposalDefineSupplier;
use App\Models\PmsModels\Rfp\RequestProposalDetails;
use App\Models\PmsModels\Quotations;
use App\Models\PmsModels\QuotationsItems;
use App\Models\PmsModels\Suppliers;
use App\Models\PmsModels\SupplierPaymentTerm;
use App\Models\PmsModels\RequestProposalTracking;
use App\Models\PmsModels\RequestProposalRequisitions;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB,Validator,Auth,View;
use Illuminate\Support\Facades\Mail;
use PDF,URL;

class RequestProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Request Proposal List';
        try {
            $requestProposals=RequestProposal::with('defineToSupplier','requestProposalDetails','requestProposalDetails.product','createdBy','relQuotations')
            ->whereNotIn('quotation_generate_type',['complete'])
            ->paginate(20);

            return view('pms.backend.pages.rfp.index', compact('title','requestProposals'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function requisitionIndex(){

        $title = 'RFP Requisition List';

        try {
            $requisitionData=Requisition::where(function($query){
                return $query->where([
                    'status'=>1,
                    'approved_id'=>1,
                    'is_send_to_rfp'=>'yes',
                    'delivery_status'=>'processing',
                    'is_po_generate'=>'no',
                ])->orWhere('request_status','rfp');
            })
            ->paginate(30);

            foreach ($requisitionData as $key=>$requisition){
                $requisition['requisition_qty']=$requisition->requisitionItems->sum('qty');

                $requisition->relRequisitionDelivery->each(function ($item,$i){
                    $item['delivery_qty']= $item->relDeliveryItems->sum('delivery_qty');
                });
                $requisition['total_delivery_qty']=$requisition->relRequisitionDelivery->sum('delivery_qty');
            }

            $userList=Requisition::join('users','users.id','=','requisitions.author_id')
            ->groupBy('requisitions.author_id')
            ->where(function ($query){
               return $query->where(['requisitions.status'=>1,'requisitions.approved_id'=>1,'requisitions.is_send_to_rfp'=>'yes','requisitions.delivery_status'=>'processing','requisitions.is_po_generate'=>'no'])->orWhere('requisitions.request_status','rfp');
            })
            ->get(['users.id','users.name']);

            return view('pms.backend.pages.rfp.deft-requisition-index',compact('title','requisitionData','userList'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

     /**
     * Rfp list view serarch.
     * Search between from and to date and also user can search by employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function rfpRequisitionListViewSearch(Request $request)
     {
        $response = [];

        $fromDate=date('Y-m-d',strtotime($request->from_date));
        $toDate=date('Y-m-d',strtotime($request->to_date));

        $requisitionBy=$request->requisition_by;
       
        $requisitionData=Requisition::whereDate('requisition_date', '>=', $fromDate)
        ->whereDate('requisition_date', '<=', $toDate)
        ->when($requisitionBy, function($query) use($requisitionBy){
            return $query->where('author_id',$requisitionBy);
        })
        ->where(function ($query){
             return $query->where(['status'=>1,'approved_id'=>1,'is_send_to_rfp'=>'yes','delivery_status'=>'processing'])->orWhere('request_status','rfp');
        })
        ->paginate(30);

        foreach ($requisitionData as $key=>$requisition){
                $requisition['requisition_qty']=$requisition->requisitionItems->sum('qty');

                $requisition->relRequisitionDelivery->each(function ($item,$i){
                    $item['delivery_qty']= $item->relDeliveryItems->sum('delivery_qty');
                });
                $requisition['total_delivery_qty']=$requisition->relRequisitionDelivery->sum('delivery_qty');
            }

        try {
            if(count($requisitionData)>0)
            {
                $body = View::make('pms.backend.pages.rfp.rfp-search-result-view',
                    ['requisitionData'=> $requisitionData]);
                
                $response['result'] = 'success';
                $response['body'] = $body->render();
            }else{
                $response['body'] = '';
                $response['result'] = 'error';
                $response['message'] = 'Data not found.!!';
            }

        }catch (\Throwable $th){
            $response['result'] = 'error';
            $response['message'] = $th->getMessage();
        }

        return $response;
    }

    /**
     * Convert to request for proposal.
     *
     * @return \Illuminate\Http\Response
    */

    public function convertToRfp(Request $request)
    {
        $response = [];

        $requisition=Requisition::where(function($query) use($request){
           return $query->where(['id'=>$request->requisition_id,'status'=>1,'approved_id'=>1,'is_send_to_rfp'=>'yes','delivery_status'=>'processing'])->orWhere('request_status','rfp');
        })
        ->first();

        //Start transaction
        DB::beginTransaction();

        try {
            if(count((array)$requisition)>0)
            {
                if($requisition->request_status=='rfp'){
                    $requisition->request_status = 'send_rfp';
                }else{
                    $requisition->delivery_status = 'rfp';
                }

                $requisition->save();
                DB::commit();

                $response['result'] = 'success';
                $response['message'] = 'Successfully Converted to RFP!!';
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $title = 'Proposal Create';
        $prefix='RP-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
        $refNo=uniqueCode(14,$prefix,'request_proposals','id');

        try {
            
            $requisitionIds = getMergedRequisisionID([
                'is_po_generate' => 'no',
                'is_send_to_rfp' => 'yes',
                'delivery_status' => 'rfp',
                'approved_id' => 1,
                'status' => 1,
            ],[
                'is_po_generate' => 'no',
                'is_send_to_rfp' => 'yes',
                'delivery_status' => 'partial-delivered',
                'approved_id' => 1,
                'status' => 1,
                'request_status' => 'send_rfp',
            ]);

            //return $requisitionIds;
            $products = Product::whereHas('requisitionItem', function($query) use($requisitionIds){
                return $query->where('is_send','no')->whereHas('requisition', function($query) use($requisitionIds){
                    return $query->whereIn('id',$requisitionIds);
                });
            });

            $productIds = $products->pluck('id')->all();

            $products = $products->get();

            $supplierList=Suppliers::where('status', 'Active')
            ->whereHas('products', function($query) use($productIds){
               return $query->whereIn('product_id',$productIds);
            })->pluck('name','id')->all();

            return view('pms.backend.pages.rfp.create', compact('title','products','supplierList','refNo','requisitionIds'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Create separate proposal.
     *
     * @return \Illuminate\Http\Response
     */

    public function createSeparate()
    {
        $title = 'Proposal Create';
        $prefix='RP-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
        $refNo=uniqueCode(14,$prefix,'request_proposals','id');

        try {

            $requisitionIds = getMergedRequisisionID([
                'is_po_generate' => 'no',
                'is_send_to_rfp' => 'yes',
                'delivery_status' => 'rfp',
                'approved_id' => 1,
                'status' => 1,
            ],[
                'is_po_generate' => 'no',
                'is_send_to_rfp' => 'yes',
                'delivery_status' => 'partial-delivered',
                'approved_id' => 1,
                'status' => 1,
                'request_status' => 'send_rfp',
            ]);
            
            $requisitions=Requisition::whereIn('id', $requisitionIds)
            ->whereHas('requisitionItems', function($query){
                return $query->where('is_send', 'no');
            })
            ->get();

            $productIds=[];
            foreach($requisitions as $values){
                foreach($values->requisitionItems()->where('is_send','no')->get() as $items){
                    array_push($productIds,$items->product_id);
                }
            }

            $supplierList=Suppliers::where('status', 'Active')
            ->whereHas('products', function($query) use($productIds){
               return $query->whereIn('product_id',$productIds);
            })
            ->pluck('name','id')
            ->all();

            return view('pms.backend.pages.rfp.create-separate', compact('title','supplierList','refNo','requisitions'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function requisitionDetailByProductId($product_id){
        $title = 'Requistion details by product.';
        $product = Product::findOrFail($product_id);

        $requisitionIds = getMergedRequisisionID([
                'is_po_generate' => 'no',
                'is_send_to_rfp' => 'yes',
                'delivery_status' => 'rfp',
                'approved_id' => 1,
                'status' => 1,
            ],[
                'is_po_generate' => 'no',
                'is_send_to_rfp' => 'yes',
                'delivery_status' => 'partial-delivered',
                'approved_id' => 1,
                'status' => 1,
                'request_status' => 'send_rfp',
            ]);

        try {
            $items = RequisitionItem::where(['product_id'=>$product_id,'is_send'=>'no'])
            ->whereHas('requisition', function($query) use($requisitionIds){
                return $query->where('status', 1)->whereIn('id',$requisitionIds);
            })
            ->get();

            return view('pms.backend.pages.proposal._product-wise-requisition', compact('title','product', 'items','requisitionIds'));

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

    public function store(Requests\Pms\RequestProposalRequest $request){
        $proposalType=$request->has('type')?'online':'manual';
        DB::beginTransaction();
        try {
            $requestProposal=RequestProposal::create([
                'type'=>$proposalType,
                'reference_no'=>$request->reference_no,
                'request_date'=>date('Y-m-d',strtotime($request->request_date)),
            ]);

            foreach ($request->supplier_id as $key=>$supplier_id){
                $requestProposalDefine[]=[
                    'request_proposal_id'=>$requestProposal->id,
                    'supplier_id'=>$supplier_id,
                ];
            }

            foreach ($request->product_id as $i=>$product_id){
                $requestProposalDetails[]=[
                    'request_proposal_id'=>$requestProposal->id,
                    'product_id'=>$product_id,
                    'request_qty'=>$request->request_qty[$product_id],
                    'qty'=>$request->qty[$product_id],
                    'created_by'=>\Auth::user()->id,
                    'created_at'=>date('Y-m-d h:i'),
                ];
            }

        //For update column (Is_Send) on requisition items table
            $requisitionIds = getMergedRequisisionID([
                'is_po_generate' => 'no',
                'is_send_to_rfp' => 'yes',
                'delivery_status' => 'rfp',
                'approved_id' => 1,
                'status' => 1,
            ],[
                'is_po_generate' => 'no',
                'is_send_to_rfp' => 'yes',
                'delivery_status' => 'partial-delivered',
                'approved_id' => 1,
                'status' => 1,
                'request_status' => 'send_rfp',
            ]);

            $requisitionIdArray = array_values(array_unique(RequisitionItem::where(['is_send'=>'no'])
            ->whereIn('product_id', $request->product_id)
            ->whereHas('requisition', function($query) use($requisitionIds){
                return $query->where('status', 1)->whereIn('id',$requisitionIds);
            })
            ->pluck('requisition_id')->toArray()));

            RequisitionItem::whereIn('requisition_id', $requisitionIdArray)->whereIn('product_id', $request->product_id)->where('is_send','no')
            ->update(['is_send'=>'yes']);

            if(isset($requisitionIdArray[0])){
                foreach($requisitionIdArray as $key => $requisition_id){
                    RequestProposalRequisitions::create([
                        'requisition_id' => $requisition_id,
                        'request_proposal_id' => $requestProposal->id,
                    ]);
                }
            }

        //Request proposal define to supplier
            RequestProposalDefineSupplier::insert($requestProposalDefine);

        //Request proposal details insert
            RequestProposalDetails::insert($requestProposalDetails);

        //Request Proposal Tracking
            RequestProposalTracking::StoreRequestProposalTracking($requestProposal->id,'RFP');

        //Send mail to supplier
            //$this->mailSendToSuppliers($requestProposal->id,$request->supplier_id,$proposalType);

            DB::commit();

            return $this->backWithSuccess('Request For Proposal Successfully Created');
        }
        catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }

    public function storeSeparate(Request $request){
        $this->validate($request, [
            'request_date' => ['required', 'date'],
            'reference_no' => ['required','max:15','unique:request_proposals'],
            'supplier_id' => ['required'],
            "supplier_id.*"  => "exists:suppliers,id",
            "requisition_item_id"    => "required|min:1",
        ]);

        $proposalType=$request->has('type')?'online':'manual';

        DB::beginTransaction();
        try {

            $requestProposal = RequestProposal::create([
                'type'=>$proposalType,
                'reference_no'=>$request->reference_no,
                'request_date'=>date('Y-m-d',strtotime($request->request_date)),
            ]);

            foreach ($request->supplier_id as $key=>$supplier_id){
                $requestProposalDefine[]=[
                    'request_proposal_id'=>$requestProposal->id,
                    'supplier_id'=>$supplier_id,
                ];
            }

            $qty = [];
            if(is_array($request->qty) && count($request->qty) > 0){
                foreach($request->qty as $combined => $value){
                    if(in_array(explode('&', $combined)[1], $request->requisition_item_id)){
                        if(!isset($qty[explode('&', $combined)[0]])){
                            $qty[explode('&', $combined)[0]] = 0;
                        }
                        $qty[explode('&', $combined)[0]] += collect($value)->sum();
                    }
                }
            }

            $request_qty = [];
            if(is_array($request->request_qty) && count($request->request_qty) > 0){
                foreach($request->request_qty as $combined => $value){
                    if(in_array(explode('&', $combined)[1], $request->requisition_item_id)){
                        if(!isset($request_qty[explode('&', $combined)[0]])){
                            $request_qty[explode('&', $combined)[0]] = 0;
                        }
                        $request_qty[explode('&', $combined)[0]] += collect($value)->sum();
                    }
                }
            }


            if(is_array($qty) && count($qty) > 0){
                foreach ($qty as $product_id => $value){
                    $requestProposalDetails[]=[
                        'request_proposal_id' => $requestProposal->id,
                        'product_id' => $product_id,
                        'request_qty' => $request_qty[$product_id],
                        'qty' => $qty[$product_id],
                        'created_by' => \Auth::user()->id,
                        'created_at' => date('Y-m-d h:i'),
                    ];
                }
            }

            $requisitionIdArray = array_values(array_unique(RequisitionItem::where(['is_send'=>'no'])
            ->whereIn('id', $request->requisition_item_id)
            ->pluck('requisition_id')->toArray()));

             RequisitionItem::whereIn('id', $request->requisition_item_id)
                    ->where('is_send','no')
                    ->update(['is_send'=>'yes']);

            if(isset($requisitionIdArray[0])){
                foreach($requisitionIdArray as $key => $requisition_id){
                    RequestProposalRequisitions::create([
                        'requisition_id' => $requisition_id,
                        'request_proposal_id' => $requestProposal->id,
                    ]);
                }
            }

            //Insert request supplier
            RequestProposalDefineSupplier::insert($requestProposalDefine);
            //Insert proposal details
            RequestProposalDetails::insert($requestProposalDetails);
            //Request Proposal Tracking
            RequestProposalTracking::StoreRequestProposalTracking($requestProposal->id,'RFP');
            //Mail send to supplier
            //$this->mailSendToSuppliers($requestProposal->id,$request->supplier_id,$proposalType);
            //Db Commit
            DB::commit();

            return $this->backWithSuccess('Request For Proposal Successfully Created');

        }catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }

    }


    public function mailSendToSuppliers($requestProposalId,$supplierIds,$proposalType=null){

        $suppliers =  Suppliers::where('status', 'Active')->whereIn('id',$supplierIds)->get();

        foreach ($suppliers as $key=>$supplier){

            $requestProposal=RequestProposal::with('defineToSupplier','requestProposalDetails','requestProposalDetails.product')
            ->whereHas('defineToSupplier',function ($query)use ($supplier) {
                $query->where('request_proposal_define_suppliers.supplier_id',$supplier->id);
            })
            ->find($requestProposalId);

            $data["email"] = $supplier->email;
            $data["title"] = "Request For Proposal From MBM";
            $data["reference_no"] = $requestProposal->reference_no;
            $data["requestProposal"] = $requestProposal;
            $data["supplier"] = $supplier;
            $data["proposalType"] = $proposalType;
            $data["current_url"] = URL::to('/');

            $pdf = PDF::loadView('pms.backend.mail.request-proposal-mail', $data)->setOptions(['defaultFont' => 'sans-serif']);

            Mail::send('pms.backend.mail.rfp_mail_body', $data, function ($message) use ($data, $pdf) {
                $message->to($data["email"], $data["email"])
                ->subject($data["title"])
                ->attachData($pdf->output(), $data["reference_no"].".pdf");
            });

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RequestProposal  $requestProposal
     * @return \Illuminate\Http\Response
     */
    public function show(RequestProposal $requestProposal)
    {
        $title='Request Proposal Details';
        $requestProposal->load('defineToSupplier','defineToSupplier.supplier','requestProposalDetails','requestProposalDetails.product','requestProposalDetails.product.category','createdBy');

        return view('pms.backend.pages.rfp.request-proposal-details', compact('title','requestProposal'));
    }


    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function sendToPurchase($req_id)
    {
        $title = 'Requisition send to purchase';
        $prefix='RP-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
        $refNo=uniqueCode(14,$prefix,'request_proposals','id');

        try {

            $requisitionIds = getMergedRequisisionID([
                'is_po_generate' => 'no',
                'is_send_to_rfp' => 'yes',
                'delivery_status' => 'processing',
                'approved_id' => 1,
                'status' => 1,
            ],[
                'is_po_generate' => 'no',
                'is_send_to_rfp' => 'yes',
                'delivery_status' => 'partial-delivered',
                'approved_id' => 1,
                'status' => 1,
                'request_status' => 'rfp',
            ]);

            $requisition = RequisitionItem::whereHas('requisition', function($query) use($requisitionIds){
                return $query->whereIn('id',$requisitionIds);
            })
            ->where('is_send','no')
            ->where('po_generate','no')
            ->where('requisition_id',$req_id)
            ->get();

            $getProductIds=[];
            foreach($requisition as $data){
                array_push($getProductIds,$data->product_id);
            }

            $selectSupplierIds=DB::table('products_supplier')
            ->whereIn('product_id',$getProductIds)
            ->groupBy('supplier_id')
            ->get(['supplier_id']);

            $getSupplierIds=[];
            foreach($selectSupplierIds as $data){
                array_push($getSupplierIds,$data->supplier_id);
            }

            $supplierList= Suppliers::where('status', 'Active')->whereIn('id',$getSupplierIds)->pluck('name','id')->all();

            if ($requisition->count() > 0) {
                return view('pms.backend.pages.store.store-inventory-purchase', compact('title','requisition','req_id','supplierList','refNo','requisitionIds'));
            }else{
                return $this->backWithError('Already purchase this requisition.');
            }

        }catch (\Throwable $th){

            return $this->backWithError($th->getMessage());
        }
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function sendToPurchaseSubmit(Request $request){   
        $this->validate($request, [
            'request_date' => ['required', 'date'],
            'reference_no' => ['required','max:15','unique:request_proposals'],
            'supplier_id' => ['required'],
            "supplier_id.*"  => "exists:suppliers,id",
            "product_id"    => "required|min:1",
        ]);

        //dd($request);
        // Transaction Start Here
        DB::beginTransaction();
        try {
            //update requistion id
            $requisition=Requisition::where(['id'=>$request->requisition_id,'status'=>1,'approved_id'=>1,'is_send_to_rfp'=>'yes','delivery_status'=>'processing'])
            ->update(['delivery_status'=>'rfp','updated_by'=>\Auth::user()->id,
                'updated_at'=>date('Y-m-d h:i')]);

            $requestProposal=RequestProposal::create([
                'type'=>'direct-purchase',
                'reference_no'=>$request->reference_no,
                'request_date'=>date('Y-m-d',strtotime($request->request_date)),
            ]);

            //Generate Quotation
            $prefix='QG-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $refNo=uniqueCode(14,$prefix,'quotations','id');

            $quotationFilePath='';
            if ($request->hasFile('quotation_file'))
            {
                $quotationFilePath=$this->fileUpload($request->file('quotation_file'),'upload/quotation/pdf-file');
            }

            $quotation=Quotations::create([
                'supplier_id'=>$request->supplier_id,
                'request_proposal_id'=>$requestProposal->id,
                'reference_no'=>$refNo,
                'quotation_date'=>date('Y-m-d',strtotime($request->request_date)),
                'total_price'=>$request->sum_of_subtoal,
                'discount'=>$request->discount==null?0:$request->discount,
                'vat'=>$request->vat==null?0:$request->vat,
                'gross_price'=>$request->gross_price,
                'status'=>'active',
                'type'=>'direct-purchase',
                'is_approved'=>'processing',
                'supplier_payment_terms_id'=>$request->supplier_payment_terms_id,
                'quotation_file'=>$quotationFilePath
            ]);

            foreach ($request->product_id as $i=>$product_id){
                $quotationItemsInput[]=[
                    'quotation_id'=>$quotation->id,
                    'product_id'=>$product_id,
                    'unit_price'=>$request->unit_price[$product_id],
                    'qty'=>$request->qty[$product_id],
                    'sub_total_price'=>$request->sub_total_price[$product_id],
                    'discount'=>$request->item_discount_percent[$product_id],
                    'discount_amount'=>$request->item_discount_amount[$product_id],
                    'vat'=>$request->sub_total_vat_price[$product_id],
                    'total_price'=>($request->sub_total_price[$product_id]-$request->item_discount_amount[$product_id])+$request->sub_total_vat_price[$product_id],
                    'created_at'=>date('Y-m-d h:i'),
                ];

                $requestProposalDetailsInput[]=[
                    'request_proposal_id'=>$requestProposal->id,
                    'product_id'=>$product_id,
                    'request_qty'=>$request->request_qty[$product_id],
                    'qty'=>$request->qty[$product_id],
                    'created_by'=>\Auth::user()->id,
                    'created_at'=>date('Y-m-d h:i'),
                ];
            }

            //For update column (Is_Send) on requisition items table 
            RequisitionItem::where('requisition_id',$request->requisition_id)->whereIn('product_id',$request->product_id)->where('is_send','no')->update(['is_send'=>'yes']);
            RequestProposalRequisitions::create([
                        'requisition_id' => $request->requisition_id,
                        'request_proposal_id' => $requestProposal->id,
                    ]);
            RequestProposalDefineSupplier::insert([
                'request_proposal_id'=>$requestProposal->id,
                'supplier_id'=>$request->supplier_id,
            ]);
            //Add request proposal details data
            RequestProposalDetails::insert($requestProposalDetailsInput);
            //Add quotation items data
            QuotationsItems::insert($quotationItemsInput);
            //Notification
            $message= '<span class="notification-links" data-src="'.route('pms.quotation.quotations.cs.proposal.details',$requestProposal->id).'" data-ttile="Request Proposal Details">Reference No:'.$requestProposal->reference_no.'.Watting for Approved.</span>';

            CreateOrUpdateNotification('',getManagerInfo('Management'), $message,'unread','send-to-manager','');

            DB::commit();

            return $this->redirectBackWithSuccess('Successfully send to purchase department!!','pms.quotation.quotations.index');

        }catch (\Throwable $th){
            //If there are any exceptions, rollback the transaction`
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
        return back();
    }

    public function getSupplierPaymentTerms(Request $request, $id)
    {
         $supplierPaymentTerms = SupplierPaymentTerm::where('supplier_id', $id)->get();
         $data = '';
         if(isset($supplierPaymentTerms[0])){
            $data .='<option value="">--Select One--</option>';
            foreach($supplierPaymentTerms as $key => $paymentTerms){
                $data .='<option value="'.$paymentTerms->id.'">'.$paymentTerms->relPaymentTerm->term.'</option>';
            }
        }

        return $data;
    }

    /**
    * Complete quotation generate.
    *
    * @return \Illuminate\Http\Response
    */

    public function rfpQuotationgenerateComplete(Request $request)
    {
        $response=[];
        $data=RequestProposal::where('id',$request->req_proposal_id)->first();
        //Start transaction
        DB::beginTransaction();
        try {
            if(!empty($data))
            {
                $data->quotation_generate_type = 'complete';
                $data->save();
                //Commit data
                DB::commit();

                $response['result'] = 'success';
                $response['message'] = 'Successfully Complete This Request Proposal!!';
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
}
