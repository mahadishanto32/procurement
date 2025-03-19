<?php

namespace App\Models\PmsModels\Accounts;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
	protected $table = 'bank_accounts';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'code',
        'type',
        'name',
        'number',
        'bank_name',
        'bank_address',
        'last_reconciled_date',
        'ending_reconcile_balance',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

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
