<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
	protected $table = 'product_attributes';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'product_id',
        'attribute_option_id',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeOption()
    {
        return $this->belongsTo(AttributeOption::class);
    }
}
