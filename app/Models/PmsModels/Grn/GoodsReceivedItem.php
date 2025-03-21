<?php

namespace App\Models\PmsModels\Grn;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PmsModels\PurchaseReturn;
use App\Models\PmsModels\Product;
class GoodsReceivedItem extends Model
{
    use SoftDeletes;
	protected $table = 'goods_received_items';
	protected $primaryKey = 'id';
    protected $fillable = ['goods_received_note_id','product_id','unit_amount','qty','sub_total','discount','vat','total_amount','quality_ensure','received_qty'];

    protected $dates = [
        'created_at', 'updated_at'
    ];


    public function relGoodsReceivedNote()
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'goods_received_note_id', 'id');
    }

    public function relPurchaseOrderReturns()
    {
        return $this->hasMany(PurchaseReturn::class, 'goods_received_item_id', 'id');
    }

    public function relProduct()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

     public function relGoodsReceivedItemStockIn()
    {
        return $this->hasMany(GoodsReceivedItemStockIn::class, 'goods_received_item_id', 'id');
    }


}
