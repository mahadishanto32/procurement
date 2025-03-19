<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
	protected $table = 'attribute_options';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'attribute_id',
        'name',
        'description',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function products()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    // public function productModels()
    // {
    //     return $this->hasMany(ProductModelAttribute::class);
    // }
}
