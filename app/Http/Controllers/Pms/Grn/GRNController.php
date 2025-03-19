<?php

namespace App\Http\Controllers\Pms\Grn;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\InventoryModels\InventoryActionControl;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Warehouses;
use Illuminate\Http\Request;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Purchase\PurchaseOrderItem;
use App\Models\PmsModels\Grn\GoodsReceivedNote;
use App\Models\PmsModels\Grn\GoodsReceivedItem;
use App\Models\PmsModels\Grn\GoodsReceivedItemStockIn;
use App\Models\PmsModels\Purchase\PurchaseOrderRequisition;
use Illuminate\Support\Facades\Mail;
use DB,Auth;

class GRNController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title="Gate-In List";
        $model = PurchaseOrder::when(isset(auth()->user()->employee->as_unit_id), function($query){
                    return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
                })
            ->when(auth()->user()->hasRole('Department-Head'), function($query){
                return $query->whereHas('purchaseOrderRequisitions', function($query){
                    return $query->where('hr_department_id', auth()->user()->employee->as_department_id);
                });
            })
            ->with('relPurchaseOrderItems','relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')
            ->where('is_send','yes')
            ->orderBy('id','desc')
            ->whereHas('relGoodReceiveNote',function ($query){
                $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
            });
            
         if (isset($request->po_id)){
             $single=$model->where('id',$request->po_id)->first();
             $title.=" for P.O. Reference: $single->reference_no";

             $model->where('id',$request->po_id);
         }
        
         $purchaseOrder=$model->paginate(30);

        return view('pms.backend.pages.grn.index',compact('title','purchaseOrder'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function poListIndex()
    {
        try{

            $title="(Gate-In) Purchase Order List";
            $data = PurchaseOrder::with('relPurchaseOrderItems','relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')
                ->when(request()->has('from_date') && request()->has('to_date'), function($query){
                    return $query->where(\DB::raw('substr(`po_date`, 1, 10)'), '>=', date('Y-m-d', strtotime(request()->get('from_date'))));
                })
                ->when(request()->has('to_date'), function($query){
                    return $query->where(\DB::raw('substr(`po_date`, 1, 10)'), '<=', date('Y-m-d', strtotime(request()->get('to_date'))));
                })
                ->when(isset(auth()->user()->employee->as_unit_id), function($query){
                    return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
                })
                ->where('is_send','yes')
                ->orderBy('id','desc')
                ->paginate(30);

            if (count($data)>0){
                calculateGrnQtyAgainstPurchaseOrder($data);
            }
            return view('pms.backend.pages.grn.po-index',compact('title','data'));

        }catch(\Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }

    }

    public function poListSearch(Request $request)
    {
        $response = [];

        $from_date=date('Y-m-d', strtotime($request->from_date));
        $to_date=date('Y-m-d', strtotime($request->to_date));

        $data=PurchaseOrder::with('relPurchaseOrderItems','relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')
        ->whereDate('po_date', '>=', $from_date)
        ->whereDate('po_date', '<=', $to_date)
        ->where('is_send','yes')
        ->orderBy('id','desc')
        ->paginate(30);

        if (count($data)>0){
                calculateGrnQtyAgainstPurchaseOrder($data);
        }

        try {
            if(count($data)>0)
            {
                $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.grn.po-search',
                    ['data'=> $data]);
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

  

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createGRN($id)
    {
        try{
            $purchaseOrder = PurchaseOrder::with('relPurchaseOrderItems','relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')->where(['id'=>$id,'is_send'=>'yes'])->first();

            if (count($purchaseOrder->relGoodReceiveNote)>0){

                    $grnQty= $purchaseOrder->relPurchaseOrderItems->each(function ($item,$i)use($purchaseOrder){

                        $purchaseOrder->relGoodReceiveNote->each(function ($grnItem, $k)use($item){
                            $item['grn_qty']+=$grnItem->relGoodsReceivedItems->where('product_id',$item->product_id)->sum('qty');
                        });
                    });
            }

            //return $purchaseOrder;

            $result=$this->checkOrderQtyAndReceiveQty($purchaseOrder);
            if ($result==-1){
                return $this->backWithWarning('Received qty can not be greater than purchase order qty');
            }elseif ($result==0){
                return $this->backWithWarning('Total Product (s) Already Received');
            }

            $title="Gate-In Recieve";
            $prefix='GATE-IN-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $refNo=uniqueCode(20,$prefix,'goods_received_notes','id');

            return view('pms.backend.pages.grn.create',compact('title','purchaseOrder','refNo'));

        }catch(Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }
    }

    public function checkOrderQtyAndReceiveQty($purchaseOrder){

        $grns = GoodsReceivedNote::with('relGoodsReceivedItems')
            ->whereIn('purchase_order_id',[$purchaseOrder->id])->get();

        $totalReceiveQty=0;
        foreach ($grns as $grn){
            $totalReceiveQty+=$grn->relGoodsReceivedItems->sum('qty');
        }

        $totalOrderQty=$purchaseOrder->relPurchaseOrderItems->sum('qty');

        return ($totalOrderQty<=>$totalReceiveQty);

        /*if (count($purchaseOrder->relPurchaseOrderItems)>0){
            foreach ($purchaseOrder->relPurchaseOrderItems as $item){
                $totalReceiveQty+=$item->relReceiveProduct->sum('qty');
            }
        }*/
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        //return $request;
        $this->validate($request, [
            'received_date' => 'required|date',
            'reference_no'  => "required|unique:goods_received_notes|max:100",
            'challan'  => "required|max:100",
            'delivery_by'  => "nullable|max:200",
            'note'  => "nullable|max:500",
            'total_price'  => "required",
            'gross_price'  => "required",
            'challan_file' => 'image|mimes:jpeg,jpg,png,gif,pdf|nullable|max:5048',
        ]);

        $purchaseOrder=PurchaseOrder::findOrFail($request->purchase_order_id);

        DB::beginTransaction();
        try{

            $challanFile='';
            if ($request->hasFile('challan_file'))
            {
                $challanFile=$this->fileUpload($request->file('challan_file'),'upload/grn/challan-file');
            }

            $prefix='GRN-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $grnReferenceNo=uniqueCode(16,$prefix,'goods_received_notes','id');

            $goodsReceivedNote=GoodsReceivedNote::create(
                [
                    'purchase_order_id'=>$request->purchase_order_id,
                    'reference_no'=>$request->reference_no,
                    'grn_reference_no'=>$grnReferenceNo,
                    'challan'=>$request->challan,
                    'challan_file'=>$challanFile,
                    'total_price'=>$request->total_price,
                    'discount'=>$request->discount??0,
                    'vat'=>$request->vat??0,
                    'gross_price'=>$request->gross_price,
                    'received_date'=>date('Y-m-d',strtotime($request->received_date)),
                    'delivery_by'=>$request->delivery_by,
                    'receive_by'=>\Auth::user()->id,
                    'note '=>$request->note,
                    'created_by'=>\Auth::user()->id,
                ]
            );

            foreach ($request->product_id as $key=>$productId){

                if ($request->qty[$productId]!=0) {
                    $goodsReceiveItems[] = [
                        'goods_received_note_id' => $goodsReceivedNote->id,
                        'product_id' => $productId,
                        'unit_amount' => $request->unit_price[$productId],
                        'qty' => $request->qty[$productId],
                        'sub_total' => $request->unit_amount[$productId],
                        'discount_percentage' => $request->discount_percentage[$productId],
                        'discount' => $request->item_discount_amount[$productId],
                        'vat_percentage' => $request->vat_percentage[$productId],
                        'vat' => $request->sub_total_vat_price[$productId],
                        'total_amount' => ((isset($request->unit_amount[$productId]) ? $request->unit_amount[$productId] : 0)-(isset($request->item_discount_amount[$productId]) ? $request->item_discount_amount[$productId] : 0))+(isset($request->sub_total_vat_price[$productId]) ? $request->sub_total_vat_price[$productId] : 0),
                    ];

                }
            }

            GoodsReceivedItem::insert($goodsReceiveItems);

            $result=$this->changeReceiveStatus($request,$goodsReceivedNote);

            if ($result===false){
                return $this->backWithWarning('Received qty greater than purchase order qty');
            }

            //Notification
            $message = '<span class="notification-links" data-src="'.route('pms.grn.grn-process.show',$goodsReceivedNote->id).'?view" data-ttile="Gate-In Details">Reference No:'.$goodsReceivedNote->reference_no.'. Waiting for the Quality Ensure.</span>';

            CreateOrUpdateNotification('',getManagerInfo('Quality-Ensure',$purchaseOrder->hr_unit_id), $message,'unread','send-to-quality-manager','');


            CreateOrUpdateNotification('',getDepartmentHeadInfo($purchaseOrder->hr_unit_id, $purchaseOrder->purchaseOrderRequisitions[0]->hr_department_id), $message,'unread','send-to-department-head','');

            DB::commit();

            return $this->redirectBackWithSuccess('Successfully Gate-In','pms.grn.po.list');

        }catch (Exception $e){
            DB::rollback();
            return $this->backWithError($e->getMessage());
        }


    }

    public function changeReceiveStatus($request,$goodsReceivedNote){

        $purchaseOrder=PurchaseOrder::with('relPurchaseOrderItems')->findOrFail($request->purchase_order_id);

        $totalOrderQty=$purchaseOrder->relPurchaseOrderItems->sum('qty');

        $grns = GoodsReceivedNote::with('relGoodsReceivedItems')
            ->whereIn('purchase_order_id',[$request->purchase_order_id])->get();

        $totalReceiveQty=0;
        foreach ($grns as $grn){
            $totalReceiveQty+=$grn->relGoodsReceivedItems->sum('qty');
        }

        if ($totalOrderQty<$totalReceiveQty){
            return false;
        }

        if ($totalOrderQty==$totalReceiveQty){
            $goodsReceivedNote->update(['received_status'=>'full']);
            return true;
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
        $response=[];

        try{
            $modal = GoodsReceivedNote::with('relPurchaseOrder','relPurchaseOrder.relQuotation','relGoodsReceivedItems','relGoodsReceivedItems.relProduct')->findOrFail($id);
            if ($modal) {
                $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.grn.show',
                    ['grn'=> $modal]);
                $contents = $body->render();

                if(request()->has('view')){
                    return $contents;
                }

                $response['result'] = 'success';
                $response['body'] = $contents;
                $response['message'] = 'Successfully Generated PO';
            }else{
                $response['result'] = 'error';
                $response['message'] = 'GRN not found!!';
            }

        }catch(\Throwable $th){
            $response['result'] = 'error';
            $response['message'] = $th->getMessage();
        }

        return $response;
    }

    

    public function purchaseOrderListAgainstGrn(){
        try{
           $title="Purchase Order List Against GRN";

            $purchaseOrdersAgainstGrn = PurchaseOrder::with('relPurchaseOrderItems','relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')
            ->where('is_send','yes')
            ->whereHas('relGoodReceiveNote',function ($query){
                $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
            })
            ->when(isset(auth()->user()->employee->as_unit_id), function($query){
                    return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
                })
            ->orderBy('id','desc')
            ->paginate(100);

            if (count($purchaseOrdersAgainstGrn)>0){
                calculateGrnQtyAgainstPurchaseOrder($purchaseOrdersAgainstGrn);
            }

            return view('pms.backend.pages.grn.po-list-against-grn',compact('title','purchaseOrdersAgainstGrn'));

        }catch(\Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }
    }

    public function grnProcessSearch(Request $request)
    {
        $response = [];
       
        $from_date=date('Y-m-d', strtotime($request->from_date));
        $to_date=date('Y-m-d', strtotime($request->to_date));
        $received_status=$request->received_status;

        $purchaseOrder=PurchaseOrder::with('relPurchaseOrderItems','relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')
            ->where('is_send','yes')
            ->whereHas('relGoodReceiveNote',function ($query){
                $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
            })
            ->when(isset(auth()->user()->employee->as_unit_id), function($query){
                return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
            })
            ->when(auth()->user()->hasRole('Department-Head'), function($query){
                return $query->whereHas('purchaseOrderRequisitions', function($query){
                    return $query->where('hr_department_id', auth()->user()->employee->as_department_id);
                });
            })
            ->whereDate('po_date', '>=', $from_date)
            ->whereDate('po_date', '<=', $to_date)
            ->orderBy('id','desc')
            ->paginate(30);

        try {
            if(count($purchaseOrder)>0)
            {
                $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.grn.grn-list-search',
                    ['purchaseOrder'=> $purchaseOrder,'received_status'=>$received_status]);
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


}
