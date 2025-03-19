<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UsersWarehouses extends Model
{
	protected $table = 'users_warehouses';
	protected $primaryKey = 'id';
    
    protected $fillable = [
        'user_id', 'warehouse_id'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function relUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function relWarehouse()
    {
        return $this->belongsTo(Warehouses::class, 'warehouse_id', 'id');
    }

}
