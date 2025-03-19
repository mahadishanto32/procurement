<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class SupplierLedgers extends Model
{
	protected $table = 'supplier_ledgers';
	protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = ['supplier_payment_id','date','opening_balance','debit','credit','closing_balance'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function relSupplierPayment()
    {
        return $this->hasOne(SupplierPayment::class, 'id', 'supplier_payment_id');
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
