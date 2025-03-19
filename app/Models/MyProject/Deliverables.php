<?php

namespace App\Models\MyProject;

use App\Models\Hr\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Deliverables extends Model
{
	protected $table = 'deliverables';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $fillable = ['project_id', 'name', 'weightage', 'status_at', 'start_at', 'end_at', 'budget'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function subDeliverables()
    {
        return $this->hasMany(SubDeliverables::class, 'deliverable_id', 'id');
    }

//    public function departments()
//    {
//        return $this->morphToMany(Department::class, 'alignable', 'alignables', 'hr_department_area_id','alignable_id');
//    }

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
