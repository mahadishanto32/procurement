<?php

namespace App\Models\PmsModels\Grn;

use Illuminate\Database\Eloquent\Model;
use App\Models\PmsModels\Grn\GoodsReceivedItem;
use App\Models\PmsModels\Warehouses;
use App\Models\PmsModels\Purchase\PurchaseOrder;

class GoodsReceivedItemStockIn extends Model
{
	protected $table = 'goods_received_items_stock_in';
	protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = [
        'purchase_order_id',
        'goods_received_item_id',
        'warehouse_id',
        'reference_no',
        'unit_amount',
        'received_qty',
        'sub_total',
        'discount_percentage',
        'discount',
        'vat_percentage',
        'vat',
        'total_amount',
        'is_grn_complete'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function relGoodsReceivedItems()
    {
        return $this->belongsTo(GoodsReceivedItem::class, 'goods_received_item_id', 'id');
    }

    public function relWarehouse()
    {
        return $this->belongsTo(Warehouses::class, 'warehouse_id', 'id');
    }

    public function relPurchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
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
