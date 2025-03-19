<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class SupplierBankAccount extends Model
{
	protected $table = 'supplier_bank_accounts';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'supplier_id',
        'account_name',
        'account_number',
        'swift_code',
        'bank_name',
        'branch',
        'currency',
        'security_check',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
