<?php

namespace App\Models\MyProject;

use App\Models\Hr\Department;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProjectTask extends Model
{
	protected $table = 'project_tasks';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $fillable = ['sub_deliverable_id', 'name', 'status', 'hour', 'initiate_time_line', 'end_time_line', 'remarks', 'weightage', 'user_id'];

    public function subDeliverable()
    {
        return $this->belongsTo(SubDeliverables::class, 'sub_deliverable_id', 'id');
    }

    public function departments()
    {
        return $this->morphToMany(Department::class, 'alignable');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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
