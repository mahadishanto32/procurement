<?php

namespace App\Models\MyProject;

use App\Models\Hr\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SubDeliverables extends Model
{
	protected $table = 'sub_deliverables';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $fillable = ['deliverable_id', 'name', 'weightage', 'status_at', 'start_at', 'end_at'];

    public function deliverable()
    {
        return $this->belongsTo(Deliverables::class, 'deliverable_id', 'id');
    }

    public function projectTasks()
    {
        return $this->hasMany(ProjectTask::class, 'sub_deliverable_id', 'id');
    }

    public function departments()
    {
        return $this->morphToMany(Department::class, 'alignable');
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
