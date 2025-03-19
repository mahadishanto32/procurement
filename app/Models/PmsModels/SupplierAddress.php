<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class SupplierAddress extends Model
{
	protected $table = 'supplier_addresses';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'supplier_id',
        'type',
        'road',
        'village',
        'city',
        'country',
        'zip',
        'address',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
