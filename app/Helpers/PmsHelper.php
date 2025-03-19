<?php

use App\Models\PmsModels\InventoryModels\InventoryLogs;
use App\Models\PmsModels\Requisition;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\SupplierLog;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Attribute;
use App\Models\PmsModels\AttributeOption;
use App\Models\PmsModels\ProductAttribute;
use App\Models\PmsModels\Notification;
use App\User;

include 'DashboardHelper.php';

function saveSupplierLog($supplier_id, $date, $topic, $text, $id = 0){
	$log = SupplierLog::find($id);
	if(isset($log->id)){
		$log->date = $date;
		$log->topic = $topic;
		$log->log = $text;
		$log->save();
	}else{
		$log = SupplierLog::create([
			'supplier_id' => $supplier_id,
			'date' => $date,
			'topic' => $topic,
			'log' => $text,
		]);
	}

	return $log;
}

function getMergedRequisisionID($condition1, $condition2){
	return array_unique(array_merge(Requisition::where($condition1)->pluck('id')->toArray(), Requisition::where($condition2)->pluck('id')->toArray()));
}

function getTypeWiseRequisistionCount($type='')
{
	return Requisition::when(isset(Auth::user()->employee->as_department_id),
		function($query){
			return $query->whereHas('relUsersList.employee',function($query){
				return $query->where('as_department_id',Auth::user()->employee->as_department_id);
			});
		})
	->where('status',$type)
	->count();
}

function inventoryStatus($type = '', $warehouse_id = 0)
{
	return InventoryLogs::where('type', $type)
	->when($warehouse_id > 0, function($query) use($warehouse_id){
		return $query->where('warehouse_id', $warehouse_id);
	})
	->count();
}

function QualityEnsureList($type)
{
	return PurchaseOrder::where('is_send','yes')
	->whereHas('relGoodReceiveNote.relGoodsReceivedItems',function ($query) use($type){
		return $query->where('quality_ensure',$type);
	})->count();
}


function spaces($value, $extra = 0){
	$spaces = '';
	for ($i=0; $i < ($value+$extra)*6; $i++) { 
		$spaces .= "&nbsp;";
	}
	return $spaces;
}

function categoryList(){
	return Category::where('parent_id',null)->get();
}

function categoryOptions($subCategories, $chosen = 0, $step = 0){
	if($step == 0){
		$subCategories = Category::doesntHave('category')->orderBy('code')->get();
	}

	$data = '';
	foreach ($subCategories as $key => $subCategory) {
		$attributeOptions = implode(',', isset(json_decode($subCategory->attributes, true)[0]) ? json_decode($subCategory->attributes, true) : []);
		$attributes = implode(',', Attribute::whereHas('options', function($query) use($subCategory){
            return $query->whereIn('id', isset(json_decode($subCategory->attributes, true)[0]) ? json_decode($subCategory->attributes, true) : []);
        })->pluck('id')->toArray());

		$data .=    '<option value="'.$subCategory->id.'" '.($chosen == $subCategory->id ? 'selected' : '').' '.($subCategory->subCategory->count() > 0 ? 'disabled' : '').' data-attributes="'.$attributes.'" data-attribute-options="'.$attributeOptions.'">'.spaces($step).$subCategory->name.'</option>';

		if($subCategory->subCategory->count() > 0){
			$data .= categoryOptions($subCategory->subCategory, $chosen, $step+1);
		}
	}

	return $data;
}

function getProductAttributes($product_id){
	return ProductAttribute::where('product_id', $product_id)->get()->pluck('attributeOption.name')->implode('-');
}

function selectedProductAttributes($product_id, $attributeOptions){
	$productAttributes = ProductAttribute::where('product_id', $product_id)->get();
	$data = '';
	if(isset($productAttributes[0])){
		foreach($productAttributes as $key => $attribute){
			$data .= ($key > 0 ? '-' : '');
			$data .= (in_array($attribute->attribute_option_id, $attributeOptions) ? '<strong>'.$attribute->attributeOption->name.'</strong>' : $attribute->attributeOption->name);
		}
	}

	return $data;
}

function unitName($id){

	return \App\Models\Hr\Unit::where('hr_unit_id',$id)->first();
}

function unreadNotification(){

	return Notification::where('user_id',auth::user()->id)
	->where('type','unread')
	->get();
}

function CreateOrUpdateNotification($item_id=null, $user_id=null, $messages, $type, $status=null, $id = 0){
	$model = Notification::find($id);
	if(isset($model->id)){
		$model->user_id = $model->user_id;
		$model->requisition_item_id = $item_id;
		$model->messages = $messages;
		$model->type = $type;
		$model->status = $status;
		$model->save();
	}else{
		$model = new Notification();
		$model->user_id = $user_id;
		$model->requisition_item_id = $item_id;
		$model->messages = $messages;
		$model->type = $type;
		$model->status = $status;
		$model->save();
	}

	return $model;
}

function checkPoAttachment($po_id,$bill_type=null,$grn_id=null){
	$nonBilled = \App\Models\PmsModels\Grn\GoodsReceivedNote::doesntHave('relPoAttachment')
	->where('purchase_order_id', $po_id)
	->count();
	if($nonBilled > 0){
		$model=\App\Models\PmsModels\Purchase\PurchaseOrderAttachment::where('purchase_order_id',$po_id)->where('status', 'pending')->where('bill_type','po')->first();

		if (isset($model->id) && $model->invoice_file != null && $model->vat_challan_file != null) {
			return $bill_type == "po" ? true : false;
		}else{
			return (\App\Models\PmsModels\Purchase\PurchaseOrderAttachment::where('purchase_order_id',$po_id)->where('status', 'approved')->where('bill_type','po')->count() >= 1 ? false : true);
		}

		return true;
	}

	return false;
}

function checkPoGrnAttachment($po_id,$status=null,$grn_id=null){

	$model=\App\Models\PmsModels\Purchase\PurchaseOrderAttachment::where('purchase_order_id',$po_id)
	->where('goods_received_note_id',$grn_id)
	->where('status','pending')
	->where('bill_type','grn')
	->first();

	if (isset($model) && $model->invoice_file !=null && $model->vat_challan_file !=null) {
		return false;
	}
	return true;
}


function calculateGrnQtyAgainstPurchaseOrder($purchaseOrder){
	foreach ($purchaseOrder as $key=>$val){
		if (isset($val->relGoodReceiveNote)){

			$grnQty= $val->relGoodReceiveNote->each(function ($item,$i){
				$item['grn_qty']= $item->relGoodsReceivedItems->sum('qty');
			});
			$val['total_grn_qty']=$val->relGoodReceiveNote->sum('grn_qty');
		}
	}

	return $purchaseOrder;
}

function status(){
	return array(
		"active"=>'Active',
		"inactive"=>'Inactive',
		"cancel"=>'Cancel',
	);
}

function statusArray(){
	return array(
		0 => "Pending",
		1 => "Approved",
		2 => "Halt",
	);
}

function statusArrayForHead(){
	return array(
		0 => "Pending",
		1 => "Acknowledge",
		2 => "Halt",
	);
}

function stringStatusArray(){
	return array(
		'pending' => "Pending",
		'approved' => "Approved",
		'halt' => "Halt",
	);
}

function deliveryStatus(){
	return array(
		'processing' => "Processing",
		'confirmed' => "Confirmed",
		'purchase' => "Purchase",
		'delivered' => "Delivered",
		'partial-delivered' => "Percial-Delivery",
		'cencel' => "Cencel",
	);
}

function maritalStatus(){
	return array(
		'Single',
		'Married',
		'Divorced',
	);
}

function bloodGroups(){
	return array(
		'N/A',
		'A+',
		'A-',
		'B+',
		'B-',
		'O+',
		'O-',
		'AB+',
		'AB-',
	);
}

function weekDays(){
	return array(
		"Monday",
		"Tuesday",
		"Wednesday",
		"Thursday",
		"Friday",
		"Saturday",
		"Sunday",
	);
}

function weekDaysIndex(){
	return array(
		"Monday" => 0,
		"Tuesday" => 1,
		"Wednesday" => 2,
		"Thursday" => 3,
		"Friday" => 4,
		"Saturday" => 5,
		"Sunday" => 6,
	);
}

function minutesDifference($from,$to)
{
	$start_date = new DateTime($from);
	$since_start = $start_date->diff(new DateTime($to));
	$minutes = $since_start->days * 24 * 60;
	$minutes += $since_start->h * 60;
	$minutes += $since_start->i;
	return $minutes;
}

function primaryApprovals(){
	return [
		[
			'name' => 'Processing',
			'class' => 'warning'
		],
		[
			'name' => 'Approved',
			'class' => 'success'
		],
		[
			'name' => 'Rejected',
			'class' => 'danger'
		],
	];
}

function uniqueCode($length,$prefix,$table,$field){
	$prefix_length = strlen($prefix);
	$max_id = DB::table($table)->max($field);
	$new = (int)($max_id);
	$new++;
	$number_of_zero = $length-$prefix_length-strlen($new);
	$zero = str_repeat("0", $number_of_zero);
	$made_id = $prefix.$zero.$new;
	return $made_id;
}

function uniqueCodeWithoutPrefix($length,$table,$field){
	$max = DB::table($table)->max($field);
	$new=(int)($max);
	$new++;
	$number_of_zero=$length-strlen($new);
	$zero=str_repeat("0", $number_of_zero);
	$made_id=$zero.$new;
	return $made_id;
}


function uniqueStringGenerator(){
	$s = 'abcdefghijklmnopqrstuvwxyz';
	$s = str_shuffle($s);
	$l = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$l = str_shuffle($l);
	$spc = '@_#-';
	$spc = str_shuffle($spc);
	$num = '0123456789';
	$num = str_shuffle($num);
	return $num.substr($spc,0,1).str_shuffle(substr($l,0,2).substr($s,0,2)).time();  
}

function ratingGenerate($totalScore='',$totalCount='')
{

	if ($totalScore > 0 && $totalCount >0) {
		//$totalMaxDecinalScore = $totalScore/ColumnCount('supplier_rattings');
		$averageRating = $totalScore/$totalCount;
	}else{
		$averageRating = 0.00;
	}

	$formatedVal = number_format($averageRating,2);
	$pieces = explode(".", $formatedVal);

	$star_list = str_repeat('<i class="fa fa-star rating-color" aria-hidden="true"></i>', number_format($pieces[0]));

	if ($pieces[1]>0) {
		$halfStart = '<i class="fa fa-star-half-o rating-color" aria-hidden="true"></i>';
	}else{
		$halfStart='';
	}

	$blankStar=(5-$formatedVal);

	$printRating='';

	for($j=1; $j <=$blankStar;$j++){
		$printRating .='<i class="fa fa-star"></i>';
	}
	
	return $star_list.''.$halfStart.''.$printRating;
}

function singleRatingGenerate($totalScore='',$totalCount='')
{

	if ($totalScore > 0 && $totalCount >0) {
		$averageRating = $totalScore/$totalCount;
	}else{
		$averageRating = 0.00;
	}

	$formatedVal = number_format($averageRating,2);
	$pieces = explode(".", $formatedVal);

	$star_list = str_repeat('<i class="fa fa-star rating-color" aria-hidden="true"></i>', number_format($pieces[0]));

	if ($pieces[1]>0) {
		$halfStart = '<i class="fa fa-star-half-o rating-color" aria-hidden="true"></i>';
	}else{
		$halfStart='';
	}

	$blankStar=(5-$formatedVal);

	$printRating='';

	for($j=1; $j <=$blankStar;$j++){
		$printRating .='<i class="fa fa-star"></i>';
	}

	return $star_list.''.$halfStart.''.$printRating;
}

function ColumnCount($table)
{
	$column = count(\Illuminate\Support\Facades\Schema::getColumnListing($table));
	return $removeExtraColumn=$column-10;
}


function supplierPaymentTerm(){

	return \App\Models\PmsModels\PaymentTerm::select('term','id')->get();
}

function supplierReceivedTerm(){
	return [
		'partial'=>'Partial Received',
		'full'=>'Full Received',
	];
}

function supplierCriteria($supplier)
{
	$data = Illuminate\Support\Facades\Schema::getColumnListing('supplier_rattings');
	$deleteDefault = [0,1,9,10,11,12,13,14,15,16];
	$keys = array_diff(array_keys($data),$deleteDefault);
	$columns = [];
	$loop=0;
	foreach ($keys as $i=> $value){
		$loop++;
		$supplierData = (object)[
			'name' => ucwords(str_replace('_',' ',$data[$value])),
			'rating' => singleRatingGenerate($supplier->SupplierRatings()->sum($data[$value]),$supplier->SupplierRatings()->count()),
			'point' => number_format(($supplier->SupplierRatings()->sum($data[$value])/$supplier->SupplierRatings()->count()),2)
		];
		$view = '<tr>
		<th>'.$loop.'</th>
		<td>'.$supplierData->name.'</td>
		<td>'.$supplierData->rating.'</td>
		<td>'.$supplierData->point.'</td>
		</tr>';
		$columns[] = $view;
	}
	return implode(' ',$columns);
}

function supplierCriteriaColumns(){
	$data = \Illuminate\Support\Facades\Schema::getColumnListing('supplier_rattings');

	$deleteDefault = [0,1,9,10,11,12,13,14,15,16];
	$keys = array_diff(array_keys($data),$deleteDefault);
	$columns = [];
	foreach ($keys as $key=>$v){
		$columns[$key]= $data[$v];
	}

	return $columns;
}

function supplierOpeningBalance($supplier_id, $date = null){
	$query = \App\Models\PmsModels\SupplierLedgers::whereHas('relSupplierPayment', function($query) use($supplier_id){
		$query->where('supplier_id', $supplier_id);
	})
	->when(!empty($date), function($query) use($date){
		$query->where('date', '<', $date);
	});

	return [
		'debit' => $query->sum('debit'),
		'credit' => $query->sum('credit'),
		'balance' => $query->sum('debit')-$query->sum('credit'),
	];
}

function deliverableWiseBudget($project_id, $deliverable_id)
{
	$totalCost = 0;
	$cdr = [];
	$requisitions = Requisition::where(['project_id' => $project_id, 'deliverable_id' => $deliverable_id])->get();
	if($requisitions->count() > 0) {
		foreach ($requisitions as $requisition) {
			foreach ($requisition->relRequisitionDelivery as $delivery) {
				$cdr[] = $delivery->reference_no;
			}
		}
		$cd = array_unique($cdr);
		$totalCost = InventoryLogs::whereIn('reference', $cd)->sum('total_price');
	}
	return $totalCost;
}

function consumedBudget($project_id)
{
	$totalCost = 0;
	$cdr = [];
	$requisitions = Requisition::where('project_id', $project_id)->get();
	if($requisitions->count() > 0) {
		foreach ($requisitions as $requisition) {
			foreach ($requisition->relRequisitionDelivery as $delivery) {
				$cdr[] = $delivery->reference_no;
			}
		}
		$cd = array_unique($cdr);
		$totalCost = InventoryLogs::whereIn('reference', $cd)->sum('total_price');
	}
	return $totalCost;
}

function downloadPDF($name,$data,$view,$paper,$orientation){
	return \PDF::loadView($view, $data)->setPaper($paper, $orientation)->setOptions(['defaultFont' => 'sans-serif'])->setOptions(array('isRemoteEnabled' => true))->setOptions(array('DOMPDF_ENABLE_CSS_FLOAT ' => true))->download($name.'-('.date('F j,Y g:i a').').pdf');
}

function getDepartmentHead($user_id){
	$user = User::find($user_id);
	$users = User::whereHas('employee', function($query) use($user){
		return $query->where('as_unit_id', $user->employee->as_unit_id)
		->where('as_department_id', $user->employee->as_department_id);
	})->get();

	$heads = [];
	if(isset($users[0])){
		foreach($users as $user){
			if($user->hasRole('Department-Head')){
				array_push($heads, $user->id);
			}
		}
	}

	$userData = User::whereIn('id', $heads)->whereHas('employee', function($query) use($user){
		return $query->where('as_unit_id', $user->employee->as_unit_id)
		->where('as_department_id', $user->employee->as_department_id);
	})->first();

	return isset($userData->id) ? $userData->id : 0;
}

function getManagerInfo($roles_name,$unit_id=null)
{
	$superPermission=['Purchase-Department','Management','Billing','Audit','Accounts','Gate Permission','Quality-Ensure', 'Store-Manager'];

	if(in_array($roles_name,$superPermission)){
		$users=User::when(!empty($unit_id),function ($query) use($unit_id){
			return $query->whereHas('employee', function ($query) use($unit_id){
				return $query->where('as_unit_id',$unit_id);
			});
		})->get();
	}else{
		$users=User::when(isset(auth::user()->employee->as_unit_id),function ($query){
			return $query->whereHas('employee', function ($query){
				return $query->where('as_unit_id',auth::user()->employee->as_unit_id)
				->where('as_department_id',auth::user()->employee->as_department_id);
			});
		})->get();
	}
	
	$heads = [];
	if(isset($users[0])){
		foreach($users as $user){
			if($user->hasRole($roles_name)){
				array_push($heads, $user->id);
			}
		}
	}

	$userData=User::whereIn('id', $heads)->first();
	
	return isset($userData->id) ? $userData->id : 0;
}

function getDepartmentHeadInfo($unit_id, $department_id)
{
	$users = User::whereHas('employee', function ($query) use($unit_id, $department_id){
			return $query->where([
				'as_unit_id' => $unit_id,
				'as_department_id' => $department_id,
			]);
	})->get();
	
	$heads = [];
	if(isset($users[0])){
		foreach($users as $user){
			if($user->hasRole('Department-Head')){
				array_push($heads, $user->id);
			}
		}
	}

	$userData = User::whereIn('id', $heads)->first();
	
	return isset($userData->id) ? $userData->id : 0;
}

function inWord($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'System only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . inWord(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . inWord($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = inWord($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= inWord($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return ucfirst($string);
}

function viewMPDF($view, $data, $title, $filename){
	\Meneses\LaravelMpdf\Facades\LaravelMpdf::loadView($view, $data, [], [
      'title'      => $title,
      'margin_top' => 0,
      'showImageErrors' => true
    ])->stream($filename.'.pdf');
}

function outputMPDF($view, $data, $title, $filename){
	return \Meneses\LaravelMpdf\Facades\LaravelMpdf::loadView($view, $data, [], [
      'title'      => $title,
      'margin_top' => 0,
      'showImageErrors' => true
    ])->output();
}

function downloadMPDF($view, $data, $title, $filename){
	\Meneses\LaravelMpdf\Facades\LaravelMpdf::loadView($view, $data, [], [
      'title'      => $title,
      'margin_top' => 0,
      'showImageErrors' => true
    ])->download($filename.'_'.date('Y-m-d g:i a').'.pdf');
}