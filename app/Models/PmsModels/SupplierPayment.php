<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Grn\GoodsReceivedNote;

class SupplierPayment extends Model
{
	protected $table = 'supplier_payments';
	protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = ['supplier_id','purchase_order_id','goods_received_note_id','transection_date','transection_type','bill_amount','pay_amount','pay_date','bill_type','status'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function relSupplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id', 'id');
    }

    public function relSupplierLedgers()
    {
        return $this->hasMany(SupplierLedgers::class, 'supplier_payment_id', 'id');
    }

    public function relPurchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function relGoodsReceivedNote()
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'goods_received_note_id', 'id');
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
