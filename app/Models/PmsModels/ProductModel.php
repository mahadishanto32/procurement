<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
	protected $table = 'product_models';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'product_id',
        'model',
        'model_name',
        'description',
        'unit_price',
        'tax',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes()
    {
        return $this->hasMany(ProductModelAttribute::class);
    }
}
