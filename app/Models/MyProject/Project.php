<?php

namespace App\Models\MyProject;

use App\Models\Hr\Department;
use App\Models\PmsModels\Requisition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use SoftDeletes;
	protected $table = 'projects';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

//teams
    protected $fillable = ['indent_no', 'name', 'work_location', 'work_reason', 'details', 'items_dimension', 'type', 'status', 'approved_by', 'sponsors', 'terms', 'status_at', 'risk', 'start_date', 'end_date', 'budget'];

    public function pages()
    {
        return $this->hasMany(ProjectPage::class, 'project_id', 'id');
    }

    public function deliverables()
    {
        return $this->hasMany(Deliverables::class, 'project_id', 'id');
    }

    public function departments()
    {
        return $this->morphToMany(Department::class, 'alignable');
    }

    public function weeklyStatus()
    {
        return $this->hasMany(WeeklyStatus::class,'project_id', 'id');
    }

    public function requisition()
    {
        return $this->hasMany(Requisition::class,'project_id', 'id');
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
