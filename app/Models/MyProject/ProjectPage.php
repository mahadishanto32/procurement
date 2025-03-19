<?php

namespace App\Models\MyProject;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ProjectPage extends Model
{
    use SoftDeletes;
	protected $table = 'project_pages';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $fillable = ['project_id', 'page', 'type', 'weightage', 'start_date', 'end_date'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

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
