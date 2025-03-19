<?php

namespace App\Models\MyProject;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WeekDay extends Model
{
	protected $table = 'week_days';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = ['name', 'work_on', 'report_on', 'hour'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    // TODO :: boot
    // boot() function used to insert logged user_id at 'created_by' & 'updated_by'
    public static function boot(){
        parent::boot();
        static::creating(function($query){
            if(Auth::check()){
                $query->created_by = @\Auth::user()->id;
            }
        });
        static::updating(function($query){
            if(Auth::check()){
                $query->updated_by = @\Auth::user()->id;
            }
        });
    }
}
