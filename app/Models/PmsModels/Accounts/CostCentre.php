<?php

namespace App\Models\PmsModels\Accounts;

use Illuminate\Database\Eloquent\Model;

class CostCentre extends Model
{
	protected $table = 'cost_centres';
    protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'phone',
        'email',
        'address',
        'logo',
        'banner',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function entryItems()
    {
        return $this->hasMany(EntryItem::class);
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
