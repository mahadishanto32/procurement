<?php

namespace App\Models\PmsModels\Accounts;

use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model
{
	protected $table = 'account_groups';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'accounts',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];
    
    public function parent()
    {
        return $this->hasOne(AccountGroup::class, 'id', 'parent_id');
    }

    public function childrenGroups()
    {
        return $this->hasMany(AccountGroup::class, 'parent_id', 'id')->with('childrenGroups');
    }

    public function chartOfAccounts()
    {
        return $this->hasMany(ChartOfAccount::class);
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
