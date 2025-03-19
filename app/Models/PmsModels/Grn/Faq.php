<?php

namespace App\Models\PmsModels\Grn;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
	
	protected $primaryKey = 'id';
    protected $guarded = [];
    protected $table = 'faq';
    protected $fillable = ['category_id','name','status'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function category()
    {
        return $this->belongsTo(\App\Models\PmsModels\Category::class);
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
