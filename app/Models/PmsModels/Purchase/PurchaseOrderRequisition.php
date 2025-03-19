<?php

namespace App\Models\PmsModels\Purchase;

use Illuminate\Database\Eloquent\Model;
use App\Models\PmsModels\Purchase\PurchaseOrder;

class PurchaseOrderRequisition extends Model
{
    protected $table='purchase_order_requisitions';

    protected $fillable = [
        'purchase_order_id',
        'requisition_id',
        'hr_department_id',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class,'purchase_order_id', 'id');
    }

    public function requisition()
    {
        return $this->belongsTo(\App\Models\PmsModels\Requisition::class);
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Hr\Department::class, 'hr_department_id', 'hr_department_id');
    }
}
