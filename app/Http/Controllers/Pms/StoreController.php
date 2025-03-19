<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;

use App\Models\PmsModels\Product;
use App\Models\PmsModels\Requisition;
use App\Models\PmsModels\RequisitionDelivery;
use App\Models\PmsModels\RequisitionDeliveryItem;
use App\Models\PmsModels\RequisitionItem;
use App\Models\PmsModels\RequisitionTracking;
use App\Models\PmsModels\RequisitionType;
use App\Models\PmsModels\Suppliers;
use App\Models\PmsModels\Warehouses;
use App\Models\PmsModels\Notification;
use App\Models\PmsModels\InventoryModels\InventorySummary;
use App\Models\PmsModels\InventoryModels\InventoryDetails;
use App\Models\PmsModels\InventoryModels\InventoryLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use DB,Validator, Str;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($deliveryStatus=null)
    {
        try {
            $title = (!empty($deliveryStatus) ? 'Complete' : 'Pending').' Delivery list for Requisition';
            $deliveryStatus=$deliveryStatus??'partial-delivered';

            $requisitions=Requisition::with('relUsersList','requisitionItems','relRequisitionDelivery','relRequisitionDelivery.relDeliveryItems')
            ->when(isset(auth()->user()->employee->as_unit_id), function($query){
                return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
            })
            ->where(['status'=>1,'delivery_status'=>$deliveryStatus])
            ->paginate(30);

            foreach ($requisitions as $key=>$requisition){
                $requisition['requisition_qty']=$requisition->requisitionItems->sum('qty');

                $requisition->relRequisitionDelivery->each(function ($item,$i){
                    $item['delivery_qty']= $item->relDeliveryItems->sum('delivery_qty');
                });
                $requisition['total_delivery_qty']=$requisition->relRequisitionDelivery->sum('delivery_qty');
            }

            return view('pms.backend.pages.requisition-delivery.index', compact('title','requisitions'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function storeRequisitionListView()
    {
        try {
            $title = 'Store Requisition List View';

            $department=Requisition::when(isset(auth()->user()->employee->as_unit_id), function($query){
                return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
            })
            ->join('users','users.id','=','requisitions.author_id')
            ->join('hr_as_basic_info','hr_as_basic_info.associate_id','=','users.associate_id')
            ->join('hr_department','hr_department.hr_department_id','=','hr_as_basic_info.as_department_id')
            ->groupBy('hr_department.hr_department_id')
            ->where(['status'=>1,'delivery_status'=>'processing','is_send_to_rfp'=>'no'])
            ->whereNotIn('delivery_status',['delivered','partial-delivered'])
            ->get(['hr_department.hr_department_id','hr_department.hr_department_name']);


            $requistionData=Requisition::when(isset(auth()->user()->employee->as_unit_id), function($query){
                return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
            })
            ->with('relUsersList')
            ->where(['status'=>1,'delivery_status'=>'processing','is_send_to_rfp'=>'no'])
            ->whereNotIn('delivery_status',['delivered','partial-delivered'])
            ->orderBy('id','DESC')
            ->paginate(30);

            return view('pms.backend.pages.store.store-requisition-list-view', compact('title','department','requistionData'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }


    /**
     * Rfp list view serarch.
     * Search between from and to date and also user can search by employee
     */
    public function departmentWiseEmployee(Request $request)
    {
        $response=[];
        $response['data']='';

        $departmentId = $request->department_id;

        $employee=Requisition::whereHas('relUsersList.employee',function($query) use($departmentId){
            return $query->where('as_department_id',$departmentId);
        })
        ->groupBy('author_id')
        ->where(['status'=>1,'delivery_status'=>'processing','is_send_to_rfp'=>'no'])
        ->whereNotIn('delivery_status',['delivered','partial-delivered'])
        ->get();

        $response['data'] .= '<option value="">--Select One--</option>';
        if (!empty($employee)) {
            foreach ($employee as $values) {
               $response['data'] .= '<option value="' . $values->relUsersList->id . '">' . $values->relUsersList->name . '</option>';
           }
       }else{
           $response['data'] .= "<option value=''>No Employee Found!!</option>";
       }
       
       $response['result'] = 'success';

       return $response;
   }


     /**
     * Rfp list view serarch.
     * Search between from and to date and also user can search by employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function storeRequisitionListViewSearch(Request $request)
     {
        $response = [];

        $fromDate=date('Y-m-d', strtotime($request->from_date));
        $toDate=date('Y-m-d', strtotime($request->to_date));

        $requisitionBy=$request->requisition_by;
        $requisitionStatus=$request->requisition_status;

        $requistionData=Requisition::when(isset(auth()->user()->employee->as_unit_id), function($query){
            return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
        })
        ->whereDate('requisition_date', '>=', $fromDate)
        ->whereDate('requisition_date', '<=', $toDate)
        ->when($requisitionBy, function($query) use($requisitionBy){
            return $query->where('author_id',$requisitionBy);
        })
        ->where(['status'=>$requisitionStatus,'delivery_status'=>'processing','is_send_to_rfp'=>'no'])
        ->paginate(30);

        try {
            if(count($requistionData)>0)
            {
                $body = View::make('pms.backend.pages.store.store-search-result-view',
                    ['requistionData'=> $requistionData]);
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

    public function rfpRequisitionList(Request $request)
    {
       try {

        $title = 'RFP Requisition List';


        $department=Requisition::when(isset(auth()->user()->employee->as_unit_id), function($query){
            return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
        })
        ->join('users','users.id','=','requisitions.author_id')
        ->join('hr_as_basic_info','hr_as_basic_info.associate_id','=','users.associate_id')
        ->join('hr_department','hr_department.hr_department_id','=','hr_as_basic_info.as_department_id')
        ->groupBy('hr_department.hr_department_id')
        ->where(['status'=>1,'is_send_to_rfp'=>'yes'])
        ->whereNotIn('delivery_status',['delivered','partial-delivered'])
        ->get(['hr_department.hr_department_id','hr_department.hr_department_name']);


        $requisition =Requisition::with('relUsersList')
        ->when(isset(auth()->user()->employee->as_unit_id), function($query){
            return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
        })
        ->where(['status'=>1,'is_send_to_rfp'=>'yes'])
        ->whereNotIn('delivery_status',['delivered','partial-delivered'])
        ->orderBy('id','DESC')
        ->paginate(30);

        return view('pms.backend.pages.store.store-rfp-requisition-list', compact('title','requisition','department'));

    }catch (\Throwable $th){
        return $this->backWithError($th->getMessage());
    }
}

    /**
     * Rfp list view serarch.
     * Search between from and to date and also user can search by employee
     */
    public function rfpDepartmentWiseEmployee(Request $request)
    {
        $response=[];
        $response['data']='';

        $departmentId = $request->department_id;

        $employee=Requisition::whereHas('relUsersList.employee',function($query) use($departmentId){
            return $query->where('as_department_id',$departmentId);
        })
        ->where(['status'=>1,'is_send_to_rfp'=>'yes'])
        ->whereNotIn('delivery_status',['delivered','partial-delivered'])
        ->groupBy('author_id')
        ->get();

        $response['data'] .= '<option value="">--Select One--</option>';
        if (!empty($employee)) {
            foreach ($employee as $values) {
               $response['data'] .= '<option value="' . $values->relUsersList->id . '">' . $values->relUsersList->name . '</option>';
           }
       }else{
           $response['data'] .= "<option value=''>No Employee Found!!</option>";
       }
       
       $response['result'] = 'success';

       return $response;
   }

    /**
    * Rfp list view serarch.
    * Search between from and to date and also user can search by employee
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function rfpRequisitionSearch(Request $request)
    {
        $response = [];

        $fromDate=date('Y-m-d', strtotime($request->from_date));
        $toDate=date('Y-m-d', strtotime($request->to_date));
        $departmentId=$request->department_id;

        $requisitionBy=$request->requisition_by;
        $requisitionStatus=$request->requisition_status;

        $requisition=Requisition::whereDate('requisition_date', '>=', $fromDate)
        ->whereDate('requisition_date', '<=', $toDate)
        ->when($requisitionBy, function($query) use($requisitionBy){
            return $query->where('author_id',$requisitionBy);
        })
        ->when($departmentId, function($query) use($departmentId){
            return $query->whereHas('relUsersList.employee', function($query) use($departmentId){
               return $query->where('as_department_id',$departmentId);
           });
        })
        ->when(isset(auth()->user()->employee->as_unit_id), function($query){
            return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
        })
        ->where(['status'=>$requisitionStatus,'is_send_to_rfp'=>'yes'])
        ->whereNotIn('delivery_status',['delivered','partial-delivered'])
        ->paginate(30);

        try {
            if(count($requisition)>0)
            {
                $body = View::make('pms.backend.pages.store.rfp-search-result-view',
                    ['requisition'=> $requisition]);
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
    * Requistion item list.
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function requisitionItemsList($id){

        $title = "Requisition Items List";

        //Find requistion
        $requisition=Requisition::where('id',$id)
        ->when(isset(auth()->user()->employee->as_unit_id), function($query){
            return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
        })
        ->where(['status'=>1,'is_send_to_rfp'=>'yes'])
        ->whereNotIn('delivery_status',['delivered','partial-delivered'])
        ->first();

        try {

            return view('pms.backend.pages.store.requisition-items-list', compact('title','requisition'));

        }catch (\Throwable $th){

            return $this->backWithError($th->getMessage());
        }
    }   

    /**
    * Send notification to users.
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function sendNotificationToUsers(Request $request){

        //Validate inpute request
        $this->validate($request, [
            'items_id' => ['required'],
        ]);

        //Db transaction start
        DB::beginTransaction();

        try {

            //Crate instance of items_id
            foreach($request->items_id as $key=>$item){
                //Check requisition item
                $requisitionItem=RequisitionItem::where('requisition_id',$request->requisition_id)
                ->where('id',$item)
                ->first();

                if(!empty($requisitionItem))
                {  
                    $notification = new Notification();
                    $notification->user_id = $requisitionItem->requisition->author_id;
                    $notification->requisition_item_id = $item;

                    $message= '<span class="notification-links" data-src="'.route('pms.requisition.list.view.show',$requisitionItem->requisition->id).'?view" data-ttile="Requisition Details">Reference No:'.$requisitionItem->requisition->reference_no.' And Item Name: '.$requisitionItem->product->name.'. Please Collect Your product from store</span>';

                    $notification->messages = $message;

                    $notification->status = 'requisition';
                    $notification->save();

                }else{
                    return $this->backWithWarning('No data found');
                }
            }
            DB::commit();
            return $this->backWithSuccess('Successfully send to users');

        }catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }

        return back();
    }

    /**
    * Store inventory compare data.
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function storeInventoryCompare($id){
        try {

            $title = 'Store Inventory Compare';

            $requisition = Requisition::with('items','items.product','items.product.category')
            ->when(isset(auth()->user()->employee->as_unit_id), function($query){
                return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
            })
            ->findOrFail($id);

            $requisition['requisition_qty']=$requisition->requisitionItems->sum('qty');

            $requisition->relRequisitionDelivery->each(function ($item,$i){
                $item['delivery_qty']= $item->relDeliveryItems->sum('delivery_qty');
            });
            $requisition['total_delivery_qty']=$requisition->relRequisitionDelivery->sum('delivery_qty');

            return view('pms.backend.pages.store.store-inventory-compare', compact('title','requisition'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
    * Confirm delivery data return to store inventory compare delivery .
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function confirmDelivery($id){

        //Make prefix for confirm delivery.
        $prefix='CD-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
        $refNo=uniqueCode(14,$prefix,'requisition_deliveries','id');

        //Find requistion.
        $requisition=Requisition::with('relUsersList')
        ->when(isset(auth()->user()->employee->as_unit_id), function($query){
            return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
        })->findOrFail($id);
        //Make title.
        $title = 'Store Inventory Confirm Delivery to: '.$requisition->relUsersList->name;

        try {

            //Requistion items collect using requistionId.
            $requisitionItems = RequisitionItem::whereHas('requisition', function($query){
                return $query->whereIn('delivery_status',['processing','partial-delivered','rfp'])
                ->when(isset(auth()->user()->employee->as_unit_id), function($query){
                    return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
                });
            })
            ->where('requisition_id',$id)
            ->get();

            return view('pms.backend.pages.store.store-inventory-delivery', compact('title','requisitionItems','requisition','refNo'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
    * Confirm delivery submit/store.
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function confirmDeliverySubmit(Request $request){

        //Validate input request
        $this->validate($request, [
            'delivery_qty'  => "required|array|min:1",
            'delivery_date' => 'required|date',
            'reference_no'  => "required|unique:requisition_deliveries|max:100",
        ]);

        $countDeliveryItems = count(array_filter($request->delivery_qty,function ($deliveryQty){
            return !is_null($deliveryQty);
        }));


        if ($countDeliveryItems<=0){
            return $this->backWithWarning('Please select at least one product for delivery.');
        }

        DB::beginTransaction();
        try {

            $requisitionId=$request->requisition_id;
            $requisitionDelivery=$this->storeRequisitionDelivery($request);

            $itemTotalDeliveryQty=0;
            $totalDeliveryQty=0;
            $requisitionDeliveryItemInput = [];
            foreach ($request->delivery_qty as $productId=>$deliveryQty){
                if ($deliveryQty > 0 && isset($request->warehouse_id[$productId]) && $request->warehouse_id[$productId] > 0){

                    //Prepare pre-requisite data.
                    $product = Product::where('id',$productId)->first();
                    $requisitionItem = RequisitionItem::where('requisition_id',$requisitionId)
                    ->where('product_id',$productId)
                    ->first();

                    //(Current delivery qty + previous delivery qty)
                    $itemTotalDeliveryQty=($deliveryQty+$requisitionItem->delivery_qty);
                    $totalDeliveryQty+=$itemTotalDeliveryQty;

                    array_push($requisitionDeliveryItemInput, [
                        'requisition_delivery_id'=>$requisitionDelivery->id,
                        'warehouse_id'=>$request->warehouse_id[$productId],
                        'product_id'=>$productId,
                        'delivery_qty'=>$deliveryQty,
                    ]);

                    //Check requisition qty and delivery qty(current delivery qty + previous delivery qty)

                    if($itemTotalDeliveryQty > ($requisitionItem->qty)){
                        return $this->backWithWarning('Delivery qty is greater then requisition qty. Please Adjust It for product '.$product->name);
                    }

                    // Update item wise total delivery Qty (current delivery qty + previous delivery qty)

                    $requisitionItem->update(['delivery_qty'=>$itemTotalDeliveryQty]);

                    $result=$this->updateInventoryAndLog($request,$product,$deliveryQty,$request->warehouse_id[$productId],$requisitionDelivery->reference_no);

                    // check delivery qty and warehouse wise product qty
                    if ($result===false){ 
                        return $this->backWithWarning('Delivery qty may not grater than store qty for product '.$product->name);
                    }

                    $requisitionModel =Requisition::findOrFail($requisitionId);

                    $totalRequisitionQty=$requisitionModel->requisitionItems->sum('qty');

                    //Requistion tracking
                    RequisitionTracking::storeRequisitionTracking($requisitionModel->id,'delivered');

                    //Update requisition status (current delivery qty + previous delivery qty)
                    if ($totalDeliveryQty==$totalRequisitionQty){
                        $requisitionModel->update(['delivery_status'=>'delivered']);
                    }else{
                        $requisitionModel->update(['delivery_status'=>'partial-delivered']);
                    }

                    //Notification

                    if(!empty($requisitionItem))
                    {  
                        $notification = new Notification();
                        $notification->user_id = $requisitionItem->requisition->author_id;
                        $notification->requisition_item_id = $requisitionItem->id;

                        $message= '<span class="notification-links" data-src="'.route('pms.requisition.list.view.show',$requisitionItem->requisition->id).'?view" data-ttile="Requisition Details">Reference No:'.$requisitionItem->requisition->reference_no.' And Item Name: '.$requisitionItem->product->name.'. Successfully delivered your product.Please acknowledge it</span>';

                        $notification->messages = $message;

                        $notification->save();
                    }
                }
            }

            //Push requistion delivery item array in this model
            if(isset($requisitionDeliveryItemInput[0])){
                RequisitionDeliveryItem::insert($requisitionDeliveryItemInput);
            }

            //Commit data
            DB::commit();

            return $this->backWithSuccess('Requisition Delivery Successfully');

        }catch (Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }

    /**
    * Store Requistion delivery data.
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function storeRequisitionDelivery($request){
        return RequisitionDelivery::create([
            'requisition_id'=>$request->requisition_id,
            'reference_no'=>$request->reference_no,
            'delivery_date'=>date('Y-m-d',strtotime($request->delivery_date)),
            'note'=>$request->note,
            'delivery_by'=>Auth::user()->id,
            'created_by'=>Auth::user()->id,
        ]);
    }

    /**
    * Update inventory & log.
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function updateInventoryAndLog($request,$product,$deliveryQty,$warehouseId,$reference_no=''){

        //Update Inventory Summary
        $InventorySummary = InventorySummary::where([
            'product_id'=>$product->id
        ])->first();

        $InventorySummary->qty = ($InventorySummary->qty)-($deliveryQty);
        $InventorySummary->total_price = ($InventorySummary->qty-$deliveryQty)*$InventorySummary->unit_price;
        $InventorySummary->status = 'active';
        $InventorySummary->save();

        //Update Inventory details
        $InventoryDetails = InventoryDetails::where([
            'category_id'=>$product->category_id,
            'product_id'=>$product->id,
            'warehouse_id'=>$warehouseId,
            'hr_unit_id'=>auth()->user()->employee->as_unit_id,
        ])->first();

        if ($deliveryQty>(isset($InventoryDetails->qty) ? $InventoryDetails->qty : 0)){
            return false;
        }

        $InventoryDetails->qty = $InventoryDetails->qty-$deliveryQty;
        $InventoryDetails->total_price = ($InventoryDetails->qty-$deliveryQty)*$InventoryDetails->unit_price;
        $InventoryDetails->status = 'active';
        $InventoryDetails->save();

        //Add Trace on Invetory Logs/Transection Table
        $InventoryLogs = new InventoryLogs();
        $InventoryLogs->category_id = $product->category_id;
        $InventoryLogs->product_id = $product->id;
        $InventoryLogs->warehouse_id = $warehouseId;
        $InventoryLogs->hr_unit_id = isset(auth()->user()->employee->as_unit_id)?auth()->user()->employee->as_unit_id:null;
        $InventoryLogs->unit_price = $InventoryDetails->unit_price;
        $InventoryLogs->qty = $deliveryQty;
        $InventoryLogs->total_price = $deliveryQty*$InventoryDetails->unit_price;
        $InventoryLogs->reference = $reference_no;
        $InventoryLogs->status = 'active';
        $InventoryLogs->type = 'out';
        $InventoryLogs->save();

        return true;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function purchaseDepartment(Request $request)
    {
        $response = [];

        $requisition=Requisition::where(['id'=>$request->requisition_id,'status'=>1,'approved_id'=>1,'is_send_to_rfp'=>'no','delivery_status'=>'processing'])->first();

        //Start transaction
        DB::beginTransaction();

        try {
            if(count((array)$requisition)>0)
            {
                $requisition->is_send_to_rfp = 'yes';
                $requisition->save();
                //Tracking
                RequisitionTracking::storeRequisitionTracking($requisition->id,'processing');
                //Set Notification
                $message= '<span class="notification-links" data-src="'.route('pms.store-manage.store.inventory.compare',$requisition->id).'" data-ttile="Requisition Details">Reference No:'.$requisition->reference_no.'.Watting for Procurement/Purchase.</span>';

                CreateOrUpdateNotification('',getManagerInfo('Purchase-Department'),$message,'unread','sent-to-purchase','');
                //Commit data
                DB::commit();

                $response['result'] = 'success';
                $response['message'] = 'Successfully Send to Purchase Department!!';
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
    public function changeActionToRFP(Request $request)
    {
        $response = [];

        $requisition=Requisition::where(['id'=>$request->requisition_id,'status'=>1,'approved_id'=>1,'is_send_to_rfp'=>'no','delivery_status'=>'partial-delivered'])->first();

        //Start transaction
        DB::beginTransaction();

        try {
            if(count((array)$requisition)>0)
            {
                $requisition->is_send_to_rfp = 'yes';
                $requisition->request_status = 'rfp';
                $requisition->save();
                //Tracking
                RequisitionTracking::storeRequisitionTracking($requisition->id,'processing');

                $message= '<span class="notification-links" data-src="'.route('pms.store-manage.store.inventory.compare',$requisition->id).'" data-ttile="Requisition Details">Reference No:'.$requisition->reference_no.'.Watting for Procurement/Purchase.</span>';


                CreateOrUpdateNotification('',getManagerInfo('Purchase-Department'),$message,'unread','send-to-purchase','');
                //Commit data
                DB::commit();

                $response['result'] = 'success';
                $response['message'] = 'Successfully Send to Purchase Department!!';
            }else{
                $response['result'] = 'error';
                $response['message'] = 'Allready generated once!!';
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

    public function deliveredList()
    {
        try{
            $title="Deliverd Order List";
            $status=['delivered'];
            $warehouseIds=Auth::user()->relUsersWarehouse->pluck('id')->all();

            $deliveredRequisition = RequisitionDeliveryItem::whereIn('warehouse_id',$warehouseIds)
            ->orderBy('id','desc')
            ->whereNotIn('status',['delivered'])
            ->paginate(30);

            return view('pms.backend.pages.store.store-delivered-requisition-list', compact('title','deliveredRequisition','status'));

        }catch (\Throwable $th){

        }
    }

    /**
    * Delivered requistion acknowledge.
    *
    * @return \Illuminate\Http\Response
    */

    public function deliveredRequisitionAck(Request $request){

        $response=[];
        $warehouseIds=Auth::user()->relUsersWarehouse->pluck('id')->all();
        
        $deliveredRequisition = RequisitionDeliveryItem::whereIn('warehouse_id',$warehouseIds)
        ->whereNotIn('status',['delivered'])
        ->where('id',$request->id)
        ->first();

        try{

            if (isset($deliveredRequisition)) {

                $deliveredRequisition->status = 'delivered';
                $deliveredRequisition->save();

                $response['result'] = 'success';
                $response['message'] = 'Successfully Updated To Delivered.';

            }else{

                $response['result'] = 'error';
                $response['message'] = 'No Data Found';
            }

        }catch (\Throwable $th){

            $response['result'] = 'error';
            $response['message'] = $th->getMessage();
        }

        return $response;
    }

    public function deliveredRequisitionSearch(Request $request){
        $response = [];

        $fromDate=date('Y-m-d H:i:s',strtotime($request->from_date));
        $toDate=date('Y-m-d H:i:s',strtotime($request->to_date));

        $status=$request->status;

        $warehouseIds=Auth::user()->relUsersWarehouse->pluck('id')->all();

        $deliveredRequisition = RequisitionDeliveryItem::whereIn('warehouse_id',$warehouseIds)
        ->when($status, function($query) use($status){
            return $query->where('status',$status);
        })
        ->when($fromDate, function($query) use($fromDate){
            return $query->whereHas('relRequisitionDelivery',function($query) use($fromDate){
                return $query->whereDate('delivery_date', '>=',$fromDate);
            });
        })
        ->when($toDate, function($query) use($toDate){
            return $query->whereHas('relRequisitionDelivery',function($query) use($toDate){
                return $query->whereDate('delivery_date', '<=',$toDate);
            });
        })
        ->orderBy('id','DESC')
        ->paginate(30);

        try {

            if(count($deliveredRequisition)>0)
            {
                $body = View::make('pms.backend.pages.store.store-delivered-requisition-search',
                    ['deliveredRequisition'=> $deliveredRequisition]);
                
                $response['result'] = 'success';
                $response['body'] = $body->render();
            }else{
                $response['result'] = 'error';
                $response['message'] = 'No Data Found!!';
            }

        }catch (\Throwable $th){
            $response['result'] = 'error';
            $response['message'] = $th->getMessage();
        }

        return $response;
    }

    /**
    * Requisition delivery view.
    *
    * @return \Illuminate\Http\Response
    */

    public function requisitionDeliveryView(Request $request){
        $response = [];
        $requisition = Requisition::with('items','items.product','items.product.category')
        ->findOrFail($request->id);
        try {

            if(isset($requisition))
            {
                $body = View::make('pms.backend.pages.requisitions.show',
                    ['requisition'=> $requisition]);

                $response['result'] = 'success';
                $response['body'] = $body->render();
            }else{
                $response['result'] = 'error';
                $response['message'] = 'No Data Found!!';
            }

        }catch (\Throwable $th){
            $response['result'] = 'error';
            $response['message'] = $th->getMessage();
        }

        return $response;
    }
}

