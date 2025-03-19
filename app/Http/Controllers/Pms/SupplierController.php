<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\Merch\Supplier;
use App\Models\PmsModels\Grn\GoodsReceivedNote;
use App\Models\PmsModels\PaymentTerm;
use App\Models\PmsModels\SupplierPaymentTerm;
use App\Models\PmsModels\SupplierRatings;
use App\Models\PmsModels\Suppliers;
use App\Models\PmsModels\SupplierAddress;
use App\Models\PmsModels\SupplierBankAccount;
use App\Models\PmsModels\SupplierContactPerson;
use App\Models\PmsModels\SupplierLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $title = 'Suppliers';
            $suppliers = Suppliers::orderBy('id','DESC')->paginate(30);
            return view('pms.backend.pages.suppliers.index', compact('title','suppliers'));
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
        try {
            $title = 'Create New Supplier';
            $paymentTerms = PaymentTerm::orderBy('id','DESC')->get();

            return view('pms.backend.pages.suppliers.create', compact('title','paymentTerms'));
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
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255', 'email'],
            'phone' => ['nullable', 'string', 'max:14', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'mobile_no' => ['nullable', 'string', 'max:14', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'term_condition' => ['nullable'],
            'payment_term_id' => ['array','min:1'],

            'trade' => ['required', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'vat' => ['nullable', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'owner_nid' => ['nullable', 'string', 'max:255'],
            'owner_contact_no' => ['nullable', 'string', 'max:255'],
        ]);

        if($request->hasFile('auth_person_letter_file')){
            $this->validate($request, [
                'auth_person_letter_file' => ['mimes:pdf,png', 'max:2048'],
            ]);
        }

        if($request->hasFile('owner_photo_file')){
            $this->validate($request, [
                'owner_photo_file' => ['mimes:jpeg,jpg,png,gif', 'max:3072'],
            ]);
        }

        DB::beginTransaction();
        try {
            $inputs = $request->all();

            $supplier = Suppliers::create($inputs);
            $supplier->status = 'Inactive';
            $supplier->save();

            if (isset($request->payment_term_id[0])) {
                foreach ($request->payment_term_id as $key=>$paymentTermId){
                    $paymentTermInput[]=[
                        'supplier_id'=>$supplier->id,
                        'payment_term_id'=>$paymentTermId,
                        'payment_percent'=>$request->payment_percent[$key],
                        'day_duration'=>$request->day_duration[$key],
                        'type'=>$request->type[$key],
                    ];
                }
                SupplierPaymentTerm::insert($paymentTermInput);
            }

            if($request->hasFile('auth_person_letter_file')){
                $supplier->auth_person_letter = $this->fileUpload($request->file('auth_person_letter_file'), 'upload/supplier-authorization-letter');
                $supplier->save();
            }

            if($request->hasFile('owner_photo_file')){
                $supplier->owner_photo = $this->fileUpload($request->file('owner_photo_file'), 'upload/supplier-owner-photo');
                $supplier->save();
            }

            DB::commit();

            $notification = [
                'message' => 'A supplier has been created successfully',
                'alert-type' => 'success'
            ];
            return redirect('pms/supplier/'.$supplier->id.'/edit')->with($notification);
        }catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Suppliers $supplier)
    {
        try {
            $supplier->src = route('pms.supplier.update',$supplier->id);
            $supplier->req_type = 'put';
            $data = [
                'status' => 'success',
                'info' => $supplier
            ];

            //return $supplier;
            return response()->json($data);
        }catch (\Throwable $th){
            $data = [
                'status' => null,
                'info' => $th->getMessage()
            ];
            return response()->json($data);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $data = [
                'title' => 'Update Supplier Information',
                'paymentTerms' => PaymentTerm::orderBy('id','DESC')->select('id','term')->get(),
                'supplier' => Suppliers::findOrFail($id),
                'corporateAddress' => SupplierAddress::where(['supplier_id' => $id, 'type' => 'corporate'])->first(),
                'factoryAddress' => SupplierAddress::where(['supplier_id' => $id, 'type' => 'factory'])->first(),
                'contactPersonSales' => SupplierContactPerson::where(['supplier_id' => $id, 'type' => 'sales'])->first(),
                'contactPersonAfterSales' => SupplierContactPerson::where(['supplier_id' => $id, 'type' => 'after-sales'])->first(),
                'bankAccount' => SupplierBankAccount::where(['supplier_id' => $id])->first(),
            ];
            return view('pms.backend.pages.suppliers.edit', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Suppliers $supplier)
    {
        if(request()->has('form-type') && request()->get('form-type') == "basic"){
            $validator = \Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'max:255', 'email'],
                'phone' => ['nullable', 'string', 'max:14', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
                'mobile_no' => ['nullable', 'string', 'max:14', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
                'term_condition' => ['nullable'],
                'payment_term_id' => ['array','min:1'],

                'trade' => ['required', 'string', 'max:255'],
                'tin' => ['nullable', 'string', 'max:255'],
                'vat' => ['nullable', 'string', 'max:255'],
                'owner_name' => ['nullable', 'string', 'max:255'],
                'owner_nid' => ['nullable', 'string', 'max:255'],
                'owner_contact_no' => ['nullable', 'string', 'max:255'],
            ]);

            if (!$validator->passes()) {
                return redirect('pms/supplier/'.$supplier->id.'/edit?tab='.request()->get('form-type'))->withErrors($validator);
                ;
            }

            if($request->hasFile('auth_person_letter_file')){
                $validator = \Validator::make($request->all(), [
                    'auth_person_letter_file' => ['mimes:pdf,png', 'max:2048'],
                ]);

                if (!$validator->passes()) {
                    return redirect('pms/supplier/'.$supplier->id.'/edit?tab='.request()->get('form-type'))->withErrors($validator);
                    ;
                }
            }
            
            if($request->hasFile('owner_photo_file')){
                $validator = \Validator::make($request->all(), [
                    'owner_photo_file' => ['mimes:jpeg,jpg,png,gif', 'max:3072'],
                ]);

                if (!$validator->passes()) {
                    return redirect('pms/supplier/'.$supplier->id.'/edit?tab='.request()->get('form-type'))->withErrors($validator);
                    ;
                }
            }

            DB::beginTransaction();
            try {
                $inputs = $request->all();

                if (isset($request->payment_term_id[0])) {
                    foreach ($request->payment_term_id as $key=>$paymentTermId){
                        SupplierPaymentTerm::updateOrCreate([
                            'supplier_id' => $supplier->id,
                            'payment_term_id' => $paymentTermId,
                        ],[
                            'payment_percent' => $request->payment_percent[$key],
                            'day_duration' => $request->day_duration[$key],
                            'type' => $request->type[$key],
                        ]);
                    }
                }

                $supplier->update($inputs);

                if($request->hasFile('auth_person_letter_file')){
                    if(!empty($supplier->auth_person_letter)){
                        unlink(public_path($supplier->auth_person_letter));
                    }

                    $supplier->auth_person_letter = $this->fileUpload($request->file('auth_person_letter_file'), 'upload/supplier-authorization-letter');
                    $supplier->save();
                }

                if($request->hasFile('owner_photo_file')){
                    if(!empty($supplier->owner_photo)){
                        unlink(public_path($supplier->owner_photo));
                    }

                    $supplier->owner_photo = $this->fileUpload($request->file('owner_photo_file'), 'upload/supplier-owner-photo');
                    $supplier->save();
                }

                DB::commit();
            }catch (\Throwable $th){
                DB::rollback();
                return $this->backWithError($th->getMessage());
            }
        }

        if(request()->has('form-type') && request()->get('form-type') == "address"){
            $validator = \Validator::make($request->all(), [
                'corporate_road' => ['required', 'string', 'max:255'],
                'corporate_city' => ['required', 'string', 'max:255'],
                'corporate_zip' => ['required', 'string', 'max:255'],
                'corporate_country' => ['required', 'string', 'max:255'],
                // 'factory_road' => ['required', 'string', 'max:255'],
                // 'factory_city' => ['required', 'string', 'max:255'],
                // 'factory_zip' => ['required', 'string', 'max:255'],
                // 'factory_country' => ['required', 'string', 'max:255'],
            ]);

            if (!$validator->passes()) {
                return redirect('pms/supplier/'.$supplier->id.'/edit?tab='.request()->get('form-type'))->withErrors($validator);
                ;
            }

            DB::beginTransaction();
            try {
                SupplierAddress::updateOrCreate([
                    'supplier_id' => $supplier->id,
                    'type' => 'corporate',
                ],[
                    'road' => $request->corporate_road,
                    'village' => $request->corporate_village,
                    'city' => $request->corporate_city,
                    'country' => $request->corporate_country,
                    'zip' => $request->corporate_zip,
                    'address' => $request->corporate_address 
                ]);

                SupplierAddress::updateOrCreate([
                    'supplier_id' => $supplier->id,
                    'type' => 'factory',
                ],[
                    'road' => $request->factory_road,
                    'village' => $request->factory_village,
                    'city' => $request->factory_city,
                    'country' => $request->factory_country,
                    'zip' => $request->factory_zip,
                    'address' => $request->factory_address 
                ]);

                DB::commit();
            }catch (\Throwable $th){
                DB::rollback();
                return $this->backWithError($th->getMessage());
            }
        }

        if(request()->has('form-type') && request()->get('form-type') == "contact-person"){
            $validator = \Validator::make($request->all(), [
                'contact_person_sales_name' => ['required', 'string', 'max:255'],
                'contact_person_sales_designation' => ['required', 'string', 'max:255'],
                'contact_person_sales_mobile' => ['required', 'string', 'max:255'],
                'contact_person_after_sales_name' => ['required', 'string', 'max:255'],
                'contact_person_after_sales_designation' => ['required', 'string', 'max:255'],
                'contact_person_after_sales_mobile' => ['required', 'string', 'max:255'],
            ]);

            if (!$validator->passes()) {
                return redirect('pms/supplier/'.$supplier->id.'/edit?tab='.request()->get('form-type'))->withErrors($validator);
                ;
            }

            DB::beginTransaction();
            try {
                SupplierContactPerson::updateOrCreate([
                    'supplier_id' => $supplier->id,
                    'type' => 'sales',
                ],[
                    'name' => $request->contact_person_sales_name,
                    'designation' => $request->contact_person_sales_designation,
                    'mobile' => $request->contact_person_sales_mobile,
                    'email' => $request->contact_person_sales_email
                ]);

                SupplierContactPerson::updateOrCreate([
                    'supplier_id' => $supplier->id,
                    'type' => 'after-sales',
                ],[
                    'name' => $request->contact_person_after_sales_name,
                    'designation' => $request->contact_person_after_sales_designation,
                    'mobile' => $request->contact_person_after_sales_mobile,
                    'email' => $request->contact_person_after_sales_email
                ]);

                DB::commit();
            }catch (\Throwable $th){
                DB::rollback();
                return $this->backWithError($th->getMessage());
            }
        }

        if(request()->has('form-type') && request()->get('form-type') == "bank-account"){
            $validator = \Validator::make($request->all(), [
                'bank_account_name' => ['required', 'string', 'max:255'],
                'bank_account_number' => ['required', 'string', 'max:255'],
                'bank_swift_code' => ['required', 'string', 'max:255'],
                'bank_name' => ['required', 'string', 'max:255'],
                'bank_branch' => ['required', 'string', 'max:255'],
            ]);

            if (!$validator->passes()) {
                return redirect('pms/supplier/'.$supplier->id.'/edit?tab='.request()->get('form-type'))->withErrors($validator);
                ;
            }

            if($request->hasFile('bank_security_check_file')){
                $validator = \Validator::make($request->all(), [
                    'bank_security_check_file' => ['mimes:pdf,png', 'max:2048'],
                ]);

                if (!$validator->passes()) {
                    return redirect('pms/supplier/'.$supplier->id.'/edit?tab='.request()->get('form-type'))->withErrors($validator);
                    ;
                }
            }

            DB::beginTransaction();
            try {
                $bankAccount = SupplierBankAccount::updateOrCreate([
                    'supplier_id' => $supplier->id,
                ],[
                    'account_name' => $request->bank_account_name,
                    'account_number' => $request->bank_account_number,
                    'swift_code' => $request->bank_swift_code,
                    'bank_name' => $request->bank_name,
                    'branch' => $request->bank_branch,
                    'currency' => $request->bank_currency,
                ]);

                if($request->hasFile('bank_security_check_file')){
                    if(!empty($bankAccount->security_check)){
                        unlink(public_path($bankAccount->security_check));
                    }

                    $bankAccount->security_check = $this->fileUpload($request->file('bank_security_check_file'), 'upload/bank-guarantee-security-check');
                    $bankAccount->save();
                }

                DB::commit();
            }catch (\Throwable $th){
                DB::rollback();
                return $this->backWithError($th->getMessage());
            }
        }

        $notification = [
            'message' => 'A supplier has been updated successfully',
            'alert-type' => 'success'
        ];
        return redirect('pms/supplier/'.$supplier->id.'/edit?tab='.request()->get('form-type'))->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $supplier = Suppliers::find($id);
            
            $bankAccount = SupplierBankAccount::where('supplier_id', $supplier->id)->first();
            if(isset($bankAccount->id) && !empty($bankAccount->security_check) && file_exists(public_path($bankAccount->security_check))){
                unlink(public_path($bankAccount->security_check));
            }

            $delete = Suppliers::find($id)->delete();
            if($delete){
                if(!empty($supplier->auth_person_letter) && file_exists(public_path($supplier->auth_person_letter))){
                    unlink(public_path($supplier->auth_person_letter));
                }

                if(!empty($supplier->owner_photo) && file_exists(public_path($supplier->owner_photo))){
                    unlink(public_path($supplier->owner_photo));
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Supplier has been deleted successfully"
            ]);
        }catch (\Throwable $th){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function showSupplierProfile($id){

        $data = [
            'title' => 'Supplier Profile Information',
            'supplier' => Suppliers::findOrFail($id),
            'corporateAddress' => SupplierAddress::where(['supplier_id' => $id, 'type' => 'corporate'])->first(),
            'factoryAddress' => SupplierAddress::where(['supplier_id' => $id, 'type' => 'factory'])->first(),
            'contactPersonSales' => SupplierContactPerson::where(['supplier_id' => $id, 'type' => 'sales'])->first(),
            'contactPersonAfterSales' => SupplierContactPerson::where(['supplier_id' => $id, 'type' => 'after-sales'])->first(),
            'bankAccount' => SupplierBankAccount::where(['supplier_id' => $id])->first(),
            'logs' => SupplierLog::where('supplier_id', $id)->orderBy('id', 'desc')->get(),
        ];
        return view('pms.backend.pages.suppliers.profile', $data);
    }

    public function showSupplierRatingFrom($supplierId,$grnId){

       $supplierCriteriaColumns=supplierCriteriaColumns();

       $supplierData = Suppliers::with('relPaymentTerms','relPaymentTerms.relPaymentTerm','SupplierRatings')->findOrFail($supplierId);
       $grn = GoodsReceivedNote::findOrFail($grnId);
       $title = "Give rating to supplier the ( $supplierData->name )";
       return view('pms.backend.pages.suppliers.rating', compact('title', 'supplierData','supplierCriteriaColumns','grn'));

   }

   public function storeSupplierRating(Request $request){

    DB::beginTransaction();
    try {

        $grn=GoodsReceivedNote::findOrFail($request->grn_id);
        $supplier=Suppliers::findOrFail($request->supplier_id);
        $totalColumn = ColumnCount('supplier_rattings');
        $totalScore =0;

        $ratingInput=[];
        foreach ($request->rating as $key=>$rate){

            $ratingInput[$key]=$request->rating[$key]??0;
            $totalScore+=$ratingInput[$key];
        }

        if ($grn->is_supplier_rating=='yes'){

            //return $this->redirectBackWithWarning('Supplier Rating Already Submit','pms.grn.grn-process.index');

            return $this->redirectBackWithWarning('Supplier Rating Already Submit','pms.stockin.grn.list');
        }
        if ($totalScore<=0){
            return $this->backWithWarning('Rating is empty');
        }

        $ratingInput['created_by']=\Auth::user()->id;
        $ratingInput['supplier_id']=$request->supplier_id;
        $ratingInput['total_score']=$totalScore/$totalColumn;

        SupplierRatings::create($ratingInput);

        $grn->update(['is_supplier_rating'=>'yes']);
        DB::commit();

        return $this->redirectBackWithSuccess('Supplier Rating is successful','pms.stockin.grn.list');

    }catch (\Throwable $th){
        DB::rollback();
        return response()->json($th->getMessage());
    }

    }

    public function toggle($id)
    {
        $supplier = Suppliers::find($id);
        $supplier->status = ($supplier->status == "Active" ? "Inactive" : "Active");
        $supplier->save();
        return response()->json([
            'message' => "Supplier has been ".($supplier->status == "Active" ? "Activated" : "Inactivated")." successfully",
            'status' => $supplier->status,
        ]);
    }

    public function createSupplierLog($supplier_id)
    {
        $data = [
            'supplier_id' => $supplier_id
        ];

        return view('pms.backend.pages.suppliers.logs.create', $data);
    }

    public function saveSupplierLog(Request $request, $supplier_id)
    {
        $request->validate([
            'date' => 'required',
            'topic' => 'required',
            'log' => 'required',
        ]);

        DB::beginTransaction();
        try{
            $log = saveSupplierLog($supplier_id, $request->date, $request->topic, $request->log);

            if($log){
                DB::commit();
                return response()->json([
                    'success' => true
                ]);
            }

            return response()->json([
                'success' => false
            ]);
        }catch (\Throwable $th){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function editSupplierLog($log_id)
    {
        $data = [
            'log' => SupplierLog::find($log_id)
        ];

        return view('pms.backend.pages.suppliers.logs.edit', $data);
    }

    public function updateSupplierLog(Request $request, $log_id)
    {
        $request->validate([
            'date' => 'required',
            'topic' => 'required',
            'log' => 'required',
        ]);

        DB::beginTransaction();
        try{
            $log = SupplierLog::find($log_id);
            $log = saveSupplierLog($log->supplier_id, $request->date, $request->topic, $request->log, $log->id);

            if($log){
                DB::commit();
                return response()->json([
                    'success' => true
                ]);
            }

            return response()->json([
                'success' => false
            ]);
        }catch (\Throwable $th){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function deleteSupplierLog($log_id)
    {
        DB::beginTransaction();
        try{
            $log = SupplierLog::find($log_id)->delete();

            if($log){
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Log has been deleted'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ]);
        }catch (\Throwable $th){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
