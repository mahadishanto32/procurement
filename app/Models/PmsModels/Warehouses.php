<?php

namespace App\Models\PmsModels;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Warehouses extends Model
{
    //use HasFactory;
    protected $fillable = ['code', 'name', 'phone', 'email', 'location', 'address'];

    public function relUsersWarehouse()
    {
        return $this->belongsToMany(User::class,'users_warehouses','user_id','warehouse_id');
    }

    public function inventoryLogs()
    {
        return $this->hasMany(\App\Models\PmsModels\InventoryModels\InventoryLogs::class, 'warehouse_id', 'id');
    }
}
