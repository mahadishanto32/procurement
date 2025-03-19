<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;

use App\Models\MyProject\Deliverables;
use App\Models\PmsModels\Menu\Menu;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Requisition;
use App\Models\PmsModels\RequisitionTracking;
use App\Models\PmsModels\RequisitionItem;
use App\Models\PmsModels\RequisitionType;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\CategoryDepartment;
use App\Models\PmsModels\Notification;
use App\Models\PmsModels\RequisitionDelivery;
use App\Models\PmsModels\RequisitionDeliveryItem;
use App\Models\PmsModels\RequisitionNoteLogs;
use App\Models\MyProject\Project;
use Illuminate\Http\Request;
use View;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use DB,Validator, Str;
use Carbon\Carbon;

class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $title = 'Requisition';
            $requisitions = Requisition::with('items')
            ->when(isset(auth()->user()->employee->as_department_id), function($query){
                return $query->where('author_id',Auth::user()->id);
            })
            ->orderBy('id','DESC')
            ->whereNotIn('status', [2])
            ->paginate(30);

            return view('pms.backend.pages.requisitions.index', compact('title', 'requisitions'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function halt()
    {
        try {

            $title = 'Halt Requisitions';
            $requisitions = Requisition::with('items')
            ->when(isset(auth()->user()->employee->as_department_id), function($query){
                return $query->where('author_id',Auth::user()->id);
            })
            ->orderBy('id','DESC')
            ->whereIn('status', [2])
            ->paginate(30);

            return view('pms.backend.pages.requisitions.halt-index', compact('title', 'requisitions'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->employee){
            return redirect()->back();
        }
        try {
            $categoryId = CategoryDepartment::when(isset(auth()->user()->employee->as_department_id), function($query){
                return $query->where('hr_department_id', auth()->user()->employee->as_department_id);
            })->pluck('category_id')->toArray();

            
            $categories = Category::where('parent_id', null)->whereIn('id', $categoryId)->get();

            $subCategories = Category::whereIn('parent_id', $categoryId)->get();

            $title = 'Create Requisition';
            $requisition = null;

            $prefix='RQ-'.date('y', strtotime(date('Y-m-d'))).'-'.auth()->user()->employee->unit->hr_unit_short_name.'-';
            $refNo=uniqueCode(16,$prefix,'requisitions','id');

            $projects = [];
            foreach (Auth::user()->projectTask as $task){
                $projects[] = $task->subDeliverable->deliverable->project;
            }

            $projects = array_keys(collect($projects)->groupBy('id')->toArray());

            $projects = Project::whereIn("id", $projects)->get();

            $unitId=isset(auth()->user()->employee->as_unit_id)?auth()->user()->employee->as_unit_id:null;

            return view('pms.backend.pages.requisitions.create', compact('title', 'requisition','refNo','categories','subCategories','projects','unitId'));
        }catch (Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function loadProjectWiseDeliverables(Project $project = null)
    {
        $deliverables = [];
        foreach (Auth::user()->projectTask as $task){
            if ($task->subDeliverable->deliverable->project_id == $project->id){
                $deliverables[] = $task->subDeliverable->deliverable;
            }
        }
        $deliverables = array_keys(collect($deliverables)->groupBy('id')->toArray());
        $deliverables = Deliverables::whereIn("id", $deliverables)->get();

        $output = [];
        $output[] = "<option>Select One</option>";
        foreach ($deliverables as $deliverable){
            $output[] = '<option value="'.$deliverable->id.'">'.$deliverable->name.'</option>';
        }
        return response()->json(implode(',',$output));
    }

    public function loadCategoryWiseProducts($categoryId,Request $request){
        $response='';

        $categoryIds = CategoryDepartment::when(isset(auth()->user()->employee->as_department_id), function($query){
                return $query->where('hr_department_id', auth()->user()->employee->as_department_id);
            })->pluck('category_id')->toArray();


        $categoryProducts=Product::when(!empty($categoryId), function($query) use($categoryId){
            return $query->where(function($query) use($categoryId){
                return $query->where('category_id', $categoryId)
                             ->orWhereHas('category', function($query) use($categoryId){
                                return $query->where('parent_id', $categoryId);
                             });
            });
        })
        ->whereIn('category_id', $categoryIds);

        if (isset($request->products_id)){
            $existedProducts = explode(',',$request->products_id);
            if(request()->has('selected')){
                $existedProducts = array_diff($existedProducts, [request()->get('selected')]);
            }

            $categoryProducts = $categoryProducts->whereNotIn('id', $existedProducts);
        }

        $categoryProducts=$categoryProducts->get();

        $response .= '<select name="product_id[]" id="product_1" class="form-control select2 product" required>';
        $response .= '<option value="">--Select Product--</option>';
        if (!empty($categoryProducts)) {
            foreach ($categoryProducts as $data) {
                $response.= '<option value="' . $data->id . '" data-sub-category-id="' . $data->category_id .'" data-category-id="' . $data->category->parent_id .'" '.(request()->get('selected') == $data->id ? 'selected' : '').'>'. $data->name . ' ('.getProductAttributes($data->id).')</option>';
            }
        }else{
            $response .= "<option value=''>No Product Found!!</option>";
        }
        $response .= "</select>";

        return $response;
    }
    
     public function loadCategoryWiseSubcategory($categoryId){
        $response = '';
        $subCategory=Category::when(!empty($categoryId), function($query) use($categoryId){
            return $query->where('parent_id',$categoryId);
        })->get();

        if (isset($subCategory) && count((array)$subCategory)>0) {
            $response .= '<option value="">--Select Subcategory--</option>';
            foreach ($subCategory as $data) {
                $response.= '<option value="'.$data->id.'">'.$data->name.'('.$data->code.')'.'</option>';
            }
        }else{
            $response .= "<option value=''>No Category Found!!</option>";
        }
        
        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request,$edit=false)
    {
        DB::beginTransaction();
        try {

            $requisition=Requisition::create([
                'requisition'=>uniqueStringGenerator(),
                'reference_no'=>$request->reference_no,

                'hr_unit_id'=>isset($request->hr_unit_id)?$request->hr_unit_id:null,

                'requisition_date'=>date('Y-m-d h:i',strtotime($request->requisition_date)),
                'author_id'=>($request->approval_qty=='true')?$request->author_id:Auth::user()->id,

                'project_id'=>($request->approval_qty=='true')?$request->project_id:(isset($request->project_id)?$request->project_id:null),

                'deliverable_id' =>($request->approval_qty=='true')?$request->deliverable_id:(isset($request->deliverable_id)?$request->deliverable_id:null),
                'status'=>($request->approval_qty=='true')?0:3,
                'remarks'=>$request->remarks,
            ]);

            foreach ($request->qty as $key=>$qty){
                $requisitionItemInput[]=[
                    'requisition_id'=>$requisition->id,
                    'product_id'=>$request->product_id[$key],
                    'qty'=>$qty,
                    'requisition_qty'=>($request->approval_qty=='true')?(isset($request->old_qty[$key])?$request->old_qty[$key]:$qty):$qty,
                    'created_at'=>date('Y-m-d h:i'),
                    'created_by'=>($request->approval_qty=='true')?$request->author_id:Auth::user()->id,
                ];

            }

            RequisitionItem::insert($requisitionItemInput);

            if($request->approval_qty=='true'){
                RequisitionTracking::storeRequisitionTracking($requisition->id,'draft');
                RequisitionTracking::storeRequisitionTracking($requisition->id,'pending');
                
            }else{
                RequisitionTracking::storeRequisitionTracking($requisition->id,'draft');
            }

            //Insert notes logging
            RequisitionNoteLogs::create([
                'requisition_id'=>$requisition->id,
                'notes'=>$request->remarks,
                'type'=>($request->approval_qty=='true')?'department-head':'requisition',
            ]);

            //Tracking
            DB::commit();

            if ($edit==false){
                return $this->redirectBackWithSuccess('Requisition has been successfully applied','pms.requisition.requisition.index');
            }else{
                return $requisition;
            }
        }
        catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function show(Requisition $requisition)
    {
        try {
            $title = 'Requisition Show';
            $requisition = Requisition::with('items','items.product','items.product.category')->findOrFail($requisition->id);

            return view('pms.backend.pages.requisitions.show', compact('title','requisition'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function showRequisition($id)
    {
        try {

            $title = 'Requisition Show';
            $requisition = Requisition::with('items','items.product','items.product.category')->findOrFail($id);

            $body = View::make('pms.backend.pages.requisitions.show',
                    ['requisition'=> $requisition,'title'=> $title]);
            $contents = $body->render();

            if (request()->has('view')) {
                return $contents;
            }
            
           return response()->json($contents);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function edit(Requisition $requisition)
    {
        try {
            $title = 'Requisition Update';

            $categoryDepartmentIds = CategoryDepartment::when(isset(auth()->user()->employee->as_department_id), function($query){
                return $query->where('hr_department_id', auth()->user()->employee->as_department_id);
            })->pluck('category_id')->toArray();

            $categories = Category::where('parent_id', null)->whereIn('id', $categoryDepartmentIds)->get();
            $subCategories = Category::whereIn('parent_id', $categoryDepartmentIds)->get();

            $requisition->load('requisitionItems','requisitionItems.product');

            $projects = [];
            foreach (Auth::user()->projectTask as $task){
                $projects[] = $task->subDeliverable->deliverable->project;
            }

            $projects = array_keys(collect($projects)->groupBy('id')->toArray());

            $projects = Project::whereIn("id", $projects)->get();

            $deliverables = [];
            foreach (Auth::user()->projectTask as $task){
                if ($task->subDeliverable->deliverable->project_id == $requisition->project_id){
                    $deliverables[] = $task->subDeliverable->deliverable;
                }
            }
            $deliverables = array_keys(collect($deliverables)->groupBy('id')->toArray());
            $deliverables = Deliverables::whereIn("id", $deliverables)->get();

            return view('pms.backend.pages.requisitions.edit', compact('title', 'categories', 'subCategories', 'requisition','projects', 'deliverables'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Requisition $requisition)
    {
        //Begin db transaction.
        DB::beginTransaction();
        try {
            //Delete requisition item.
            $requisition_id = $requisition->id;
            RequisitionItem::where('requisition_id',$requisition->id)->delete();
            //Delete requistion.
            $requisition->delete();

            //Store requistions
            $requisition = $this->store($request,true);
            RequisitionNoteLogs::where('requisition_id', $requisition_id)
            ->update([
                'requisition_id' => $requisition->id,
            ]);

            //Db commit
            DB::commit();

            if (isset($request->approval_qty) && $request->approval_qty=='true'){
                return $this->redirectBackWithSuccess('Requisition has been successfully applied','pms.requisition.list.view.index');
            }else{
                return $this->redirectBackWithSuccess('Requisition has been successfully Update','pms.requisition.requisition.index');
            }
            
        }
        catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $requisition = Requisition::find($id);
        if(!in_array($requisition->status, [0, 2, 3])){
            return response()->json([
                'success' => false,
                'message' => "Requisition cannot be deleted!"
            ]);
        }

        $delete = Requisition::find($id)->delete();
        if($delete){
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Something went wrong!"
        ]);
    }

    public function destroyItem(RequisitionItem $item)
    {
        try {
            $requisition = $item->requisition;
            $item->delete();
            if ($requisition->items->count() < 1){
                $requisition->delete();
            }
            return response()->json($requisition->items->count());
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    /**
     * Rfp list view.
     */

    public function requisitionListView()
    {
        try {
            $title = 'User Requisition List';

            $requisitionUserLists=Requisition::when(isset(Auth::user()->employee->as_department_id),
                function($query){
                    return $query->whereHas('relUsersList.employee',function($query){
                        return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                    });
            })
            ->join('users','users.id','=','requisitions.author_id')
            ->groupBy('requisitions.author_id')
            // ->where(['requisitions.status'=>0,'requisitions.approved_id'=>NULL])
            ->get(['users.id','users.name']);

           $requisitionData=Requisition::when(isset(Auth::user()->employee->as_department_id), function($query){
                return $query->whereHas('relUsersList.employee',function($query){
                    return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                });
            })
            ->orderBy('id','DESC')
            ->where('status',0)
            ->paginate(30);

            return view('pms.backend.pages.requisitions.requisition-list-index', compact('title','requisitionUserLists','requisitionData'));
        }catch (Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    function needtokeep(){
        $requistions=Requisition::when(isset(Auth::user()->employee->as_department_id), function($query){
            return $query->whereHas('relUsersList.employee',function($query){
                return $query->where('as_department_id',Auth::user()->employee->as_department_id);
            });
        })
            ->where('status',0)
            ->get();

        $requistion_data = [];
        foreach ($requistions as $requistion){
            $tp=0;
            foreach ($requistion->items as $item){
                $tp += ($item->product->unit_price * $item->qty);
            };
            $requistion->total_price = $tp;
            foreach (Auth::user()->relApprovalRange as $range){
                if ($range->min_amount <= $requistion->total_price && $range->max_amount >= $requistion->total_price){
                    $requistion_data[] = $requistion;
                }
            }
        }
        return $this->paginate($requistion_data, 30);
    }

    /**
     * Rfp list view serarch.
     * Search between from and to date and also user can search by employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function requisitionListViewSearch(Request $request)
    {   
        $response = [];

        $fromDate=date('Y-m-d H:i:s',strtotime($request->from_date));
        $toDate=date('Y-m-d H:i:s',strtotime($request->to_date));

        $requisitionBy=$request->requisition_by;
        $requisitionStatus=$request->requisition_status;

        $requisitionData=Requisition::when(isset(Auth::user()->employee->as_department_id),
            function($query){
                return $query->whereHas('relUsersList.employee',function($query){
                    return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                });
        })
        ->when($requisitionBy, function($query) use($requisitionBy){
            return $query->where('author_id',$requisitionBy);
        })
        ->where('status',$requisitionStatus)
        ->whereDate('requisition_date', '>=', $fromDate)
        ->whereDate('requisition_date', '<=', $toDate)
        ->orderBy('id','DESC')
        ->paginate(30);

        try {

            if(count($requisitionData)>0)
            {
                $body = View::make('pms.backend.pages.requisitions._requisition-list-search',
                    ['requisitionData'=> $requisitionData]);
                $contents = $body->render();
                $response['result'] = 'success';
                $response['body'] = $contents;
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
     * Rfp list view.
     * Change requisiton status (Approve and rejected)
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function toggleRequisitionStatus(Request $request)
    {
        $requisition = Requisition::where('id',$request->id)->first();
        if(isset($requisition->id)){

            $newStatus = $request->status;
            $newText = $newStatus == 1 ? 'Acknowledge' : (($newStatus == 2)? 'Halt' : 'Pending');
            $newApproved = $newStatus == 1 ? 1:(($newStatus == 2)? 1 : Null);
            $newMessage= $newStatus == 1 ? 'Succesfully Updated To Acknowledgement' : (($newStatus == 2)? 'Succesfully Updated To Halt' : (($newStatus == 0)? 'Succesfully Send to Department Head':'Succesfully Updated To Pending'));

            $update = $requisition->update([
                            'status' => $newStatus,
                            'approved_id' => $newApproved,
                            'admin_remark' => $request->admin_remark,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::user()->id
                        ]);
            if ($newStatus==1) {
                RequisitionTracking::storeRequisitionTracking($requisition->id,'approved');

                $message= '<span class="notification-links" data-src="'.route('pms.store-manage.store.inventory.compare',$requisition->id).'" data-ttile="Requisition Details">Reference No:'.$requisition->reference_no.'.Watting for Procurement/Delivery.</span>';

                CreateOrUpdateNotification('',getManagerInfo('Store-Manager',$requisition->hr_unit_id),$message,'unread','send-to-store','');
                //If success then return with success message
                return $this->backWithSuccess('Succesfully Updated To Acknowledgement');
            }

            if ($newStatus==0) {
                RequisitionTracking::storeRequisitionTracking($requisition->id,'pending');
                $message= '<span class="notification-links" data-src="'.route('pms.requisition.list.view.show',$requisition->id).'" data-title="Requisition Details">Reference No:'.$requisition->reference_no.'. Watting for approval.</span>';
                
                CreateOrUpdateNotification('',getDepartmentHead($requisition->author_id),$message,'unread','send-to-department-head','');
            }

            if($update){
                return response()->json([
                    'success' => true,
                    'new_text' => $newText,
                    'message' => $newMessage
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

    public function haltRequisitionStatus(Requests\Pms\RequisitionRequest $request){

        //Find requistion
        $requisition = Requisition::findOrFail($request->id);   
        try{
            //If find then update
            $requisition->update([
                'admin_remark' => $request->admin_remark,
                'status' => 2,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id
            ]);

            $message= '<span class="notification-links" data-src="'.route('pms.requisition.list.view.show',$requisition->id).'" data-title="Requisition Details">Reference No:'.$requisition->reference_no.'. Department Head Halt This Requisition..</span>';

            CreateOrUpdateNotification('',$requisition->author_id,$message,'unread','requisition','');

            //Requistion tracking function call
            RequisitionTracking::storeRequisitionTracking($requisition->id,'halt',$request->admin_remark);
            //Nottification
            //If success then return with success message
            return $this->backWithSuccess('Requisition Successfully Halt');

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }


    public function showTracking(Request $request){

        $response=[];
        //Find requistion
        $requisition = Requisition::where('id',$request->id)->first();
        try{

            if (isset($requisition)) {

                $body = View::make('pms.backend.pages.requisitions.tracking',
                    ['requisition'=> $requisition]);

                $contents = $body->render();
                $response['result'] = 'success';
                $response['body'] = $contents;

            }else{
                $response['result'] = 'error';
                $response['message'] = 'Data not found.!!';
            }

        }catch(\Throwable $th){

            $response['result'] = 'error';
            $response['message'] = $th->getMessage();
        }

        return $response;
    }

    public function notificationAll()
    {
        try{
            $title="Notification";
            $notification = Notification::when(isset(Auth::user()->employee->as_department_id),function($query){
                return $query->whereHas('relUser.employee',function($query){
                    return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                });
            })
            ->where(function($queryHead){
                 return $queryHead->when(request()->has('search_text') && !empty(request()->get('search_text')), function($query){
                    return $query->where('messages', 'LIKE', '%'.request()->get('search_text').'%')
                    ->orWhere('type', 'LIKE', '%'.request()->get('search_text').'%');
                })->where('user_id',auth()->user()->id);
            })
            ->orderBy('id','asc')
            ->paginate(30);

            //dd($notification);

            return view('pms.backend.pages.requisitions.notification-list', compact('title','notification'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function markAsRead(Request $request)
    {
        $response=[];
        //Find notification
        $notification = Notification::where('id',$request->id)->first();

        DB::beginTransaction();
        try {

            if (isset($notification)) {
                $notification->type = 'read';
                $notification->read_at = date('Y-m-d h:i:s');
                $notification->save();

                DB::commit();
                $response['result'] = 'success';
                $response['message'] = 'Successfully Read Notification';
                $response['total_notification'] = Notification::where('type','unread')->where('user_id',auth::user()->id)->when(isset(Auth::user()->employee->as_department_id),function($query){
                return $query->whereHas('relUser.employee',function($query){
                    return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                });
            })->count();

            }else{
                $response['result'] = 'error';
                $response['message'] = 'No Notification Found';
                $response['total_notification'] = Notification::where('type','unread')->where('user_id',auth::user()->id)->when(isset(Auth::user()->employee->as_department_id),function($query){
                return $query->whereHas('relUser.employee',function($query){
                    return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                });
            })->count();
            }

        }catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }

        return $response;
    }

    public function deliveredRequisitionList()
    {
       try{
            $title="Deliverd Requisition";
            $status=['pending','acknowledge','delivered'];

            $deliveredRequisition = RequisitionDeliveryItem::when(isset(Auth::user()->employee->as_department_id),function($query){
                    return $query->whereHas('relRequisitionDelivery.relRequisition.relUsersList.employee',function($query){
                        return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                    });
                })->whereHas('relRequisitionDelivery.relRequisition',function($query){
                    return $query->where('author_id',Auth::user()->id);
                })
                ->orderBy('id','desc')
                ->where('delivery_qty', '>', 0)
                ->paginate(30);

            return view('pms.backend.pages.requisition-delivery.delivered-requisition-list', compact('title','deliveredRequisition','status'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function deliveredRequisitionAck(Request $request){   

        $response=[];

        $deliveredRequisition = RequisitionDeliveryItem::when(isset(Auth::user()->employee->as_department_id),function($query){
            return $query->whereHas('relRequisitionDelivery.relRequisition.relUsersList.employee',function($query){
                return $query->where('as_department_id',Auth::user()->employee->as_department_id);
            });
        })->whereHas('relRequisitionDelivery.relRequisition',function($query){
            return $query->where('author_id',Auth::user()->id);
        })
        ->where('id',$request->id)
        ->first();

        try{
            if (isset($deliveredRequisition)) {

                $deliveredRequisition->status = 'acknowledge';
                $deliveredRequisition->save();

                $response['result'] = 'success';
                $response['message'] = 'Successfully Acknowledged.';

                RequisitionTracking::storeRequisitionTracking($deliveredRequisition->relRequisitionDelivery->relRequisition->id,'received');

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

    public function deliveredRequisitionSearch(Request $request)
    {
        $response = [];

        $fromDate=date('Y-m-d H:i:s',strtotime($request->from_date));
        $toDate=date('Y-m-d H:i:s',strtotime($request->to_date));

        $status=$request->status;

        $deliveredRequisition = RequisitionDeliveryItem::when(isset(Auth::user()->employee->as_department_id),function($query){
            return $query->whereHas('relRequisitionDelivery.relRequisition.relUsersList.employee',function($query){
                return $query->where('as_department_id',Auth::user()->employee->as_department_id);
            });
        })->whereHas('relRequisitionDelivery.relRequisition',function($query){
            return $query->where('author_id',Auth::user()->id);
        })
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
                $body = View::make('pms.backend.pages.requisition-delivery.delivered-requisition-search',
                    ['deliveredRequisition'=> $deliveredRequisition]);
                $contents = $body->render();
                $response['result'] = 'success';
                $response['body'] = $contents;
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

