<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class ProductModelAttribute extends Model
{
	protected $table = 'product_model_attributes';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'product_model_id',
        'attribute_option_id',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function productModel()
    {
        return $this->belongsTo(ProductModel::class);
    }

    public function attributeOption()
    {
        return $this->belongsTo(AttributeOption::class);
    }
}
