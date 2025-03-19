<?php

namespace App\Models\PmsModels\Accounts;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
	protected $table = 'companies';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'code',
        'name',
        'owner_name',
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

    public function costCentres()
    {
        return $this->hasMany(CostCentre::class);
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
