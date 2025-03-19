<?php

namespace App\Models\PmsModels\Purchase;

use Illuminate\Database\Eloquent\Model;
use App\Models\PmsModels\Grn\GoodsReceivedNote;
use App\Models\PmsModels\BillingChalan;

class PurchaseOrderAttachment extends Model
{
	
    protected $table = 'purchase_order_attachment';
	protected $primaryKey = 'id';
    protected $fillable=['purchase_order_id','goods_received_note_id','invoice_file','vat_challan_file','bill_amount','bill_type','bill_number','remarks'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function relPurchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function relGoodsReceivedNote()
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'goods_received_note_id', 'id');
    }

    public function billingChallans()
    {
        return $this->hasMany(BillingChalan::class, 'purchase_order_attachment_id', 'id');
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
