<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
	protected $table = 'attributes';
	protected $primaryKey = 'id';
    protected $guarded = [];
    
    protected $fillable = [
        'name',
        'description',
        'searchable',
    ];
    
    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function options()
    {
        return $this->hasMany(AttributeOption::class);
    }
}
