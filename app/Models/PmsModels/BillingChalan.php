<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;
use App\Models\PmsModels\Purchase\PurchaseOrderAttachment;

class BillingChalan extends Model
{
	protected $table = 'billing_chalan';
	protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = ['purchase_order_attachment_id','goods_received_note_id'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function relPurchaseOrderAttachment()
    {
        return $this->belongsTo(PurchaseOrderAttachment::class, 'purchase_order_attachment_id', 'id');
    }

    public function relGRN()
    {
        return $this->belongsTo(\App\Models\PmsModels\Grn\GoodsReceivedNote::class, 'id', 'goods_received_note_id');
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
