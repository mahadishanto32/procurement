<?php

namespace App\Models\PmsModels\Accounts;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $table = 'chart_of_accounts';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = [
        'account_group_id',
        'code',
        'name',
        'type',
        'opening_balance',
        'bank_or_cash',
        'reconciliation',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function accountGroup()
    {
        return $this->belongsTo(AccountGroup::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
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
