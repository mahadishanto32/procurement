<?php

namespace App\Models\MyProject;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
	protected $table = 'holidaies';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = ['name', 'full_date', 'date', 'month', 'year', 'special_holiday'];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
