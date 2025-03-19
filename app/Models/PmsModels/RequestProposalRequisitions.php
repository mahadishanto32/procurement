<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;
use App\Models\PmsModels\Requisition;
use App\Models\PmsModels\Rfp\RequestProposal;

class RequestProposalRequisitions extends Model
{
	protected $table = 'request_proposal_requisitions';
	protected $primaryKey = 'id';

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'requisition_id',
        'request_proposal_id',
        'status'
    ];

    public function relRequisition()
    {
        return $this->belongsTo(Requisition::class,'requisition_id','id');
    }

    public function relRequestProposal()
    {
        return $this->belongsTo(RequestProposal::class, 'request_proposal_id', 'id');
    }

    //define static boot/register for created_by & updated_by
    public static function boot(){
        parent::boot();
        static::creating(function($query){
            if(\Auth::check()){
                $query->created_by = @\Auth::user()->id;
            }
        });
        static::updating(function($query){
            if(\Auth::check()){
                $query->updated_by = @\Auth::user()->id;
            }
        });
    }
}
