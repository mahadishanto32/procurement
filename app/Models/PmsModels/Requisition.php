<?php

namespace App\Models\PmsModels;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MyProject\Project;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class Requisition extends Model
{
    //use HasFactory;
    const REFNO=100;

    protected $fillable = [
        'reference_no',
        'requisition_date',
        'author_id',
        'project_id',
        'deliverable_id',
        'hr_unit_id',
        'requisition',
        'status',
        'approved_id',
        'is_send_to_rfp',
        'delivery_status',
        'request_status',
        'is_po_generate',
        'remarks',
        'delivery_note',
        'admin_remark'
    ];


    public function items()
    {
        return $this->hasMany(RequisitionItem::class, 'requisition_id', 'id');
    }

    public function relUsersList()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }
    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class, 'requisition_id', 'id');
    }

    public function requisitionTracking()
    {
        return $this->hasMany(RequisitionTracking::class, 'requisition_id', 'id');
    }

    public function relRequisitionDelivery(){
        return $this->hasMany(RequisitionDelivery::class,'requisition_id','id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function requestProposalRequisition()
    {
        return $this->hasMany(RequestProposalRequisitions::class, 'requisition_id', 'id');
    }

    public function requisitionNoteLogs()
    {
        return $this->hasMany(RequisitionNoteLogs::class, 'requisition_id', 'id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(\App\Models\PmsModels\Purchase\PurchaseOrderRequisition::class);
    }

     // TODO :: boot
    // boot() function used to insert logged user_id at 'created_by' & 'updated_by'
    public static function boot(){
        parent::boot();
        static::creating(function($query){
            if(Auth::check()){
                $query->created_by = @\Auth::user()->id;
            }
        });
        static::updating(function($query){
            if(Auth::check()){
                $query->updated_by = @\Auth::user()->id;
            }
        });
    }
}
