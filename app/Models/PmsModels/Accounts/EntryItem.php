<?php

namespace App\Models\PmsModels\Accounts;

use Illuminate\Database\Eloquent\Model;

class EntryItem extends Model
{
	protected $table = 'entry_items';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'entry_id',
        'cost_centre_id',
        'chart_of_account_id',
        'amount',
        'debit_credit',
        'reconciliation_date',
        'narration',
        'deleted_at',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    public function costCentre()
    {
        return $this->belongsTo(CostCentre::class);
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class);
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
