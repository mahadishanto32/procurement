<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class SupplierLog extends Model
{
	protected $table = 'supplier_logs';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'supplier_id',
        'date',
        'topic',
        'log',
        'created_by',
        'updated_by',
    ];

    public function supplier(){
        return $this->belongsTo(Suppliers::class);
    }

    protected $dates = [
        'created_at', 'updated_at'
    ];

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
