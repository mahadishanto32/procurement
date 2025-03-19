<?php

namespace App\Models\PmsModels\Accounts;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
	protected $table = 'entries';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'code',
        'fiscal_year_id',
        'entry_type_id',
        'tag_id',
        'number',
        'date',
        'debit',
        'credit',
        'notes',
        'deleted_at',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function items()
    {
        return $this->hasMany(EntryItem::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function entryType()
    {
        return $this->belongsTo(EntryType::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
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
