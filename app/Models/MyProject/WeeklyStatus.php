<?php

namespace App\Models\MyProject;

use Illuminate\Database\Eloquent\Model;

class WeeklyStatus extends Model
{
	protected $table = 'weekly_statuss';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = ['date', 'day', 'status', 'week_no'];

    public function project()
    {
        return $this->belongsTo(Project::class,'project_id', 'id');
    }

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
