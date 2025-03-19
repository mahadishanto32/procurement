<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class SupplierContactPerson extends Model
{
	protected $table = 'supplier_contact_persons';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'supplier_id',
        'type',
        'name',
        'designation',
        'mobile',
        'email',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
