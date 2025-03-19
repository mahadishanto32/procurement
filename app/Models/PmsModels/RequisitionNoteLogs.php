<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class RequisitionNoteLogs extends Model
{
	protected $table = 'requisition_note_logs';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'requisition_id', 
        'type',
        'notes',
        'status',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

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
