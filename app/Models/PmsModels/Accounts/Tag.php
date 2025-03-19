<?php

namespace App\Models\PmsModels\Accounts;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
	protected $table = 'tags';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'title',
        'color',
        'background',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function entries()
    {
        return $this->hasMany(Entry::class);
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
