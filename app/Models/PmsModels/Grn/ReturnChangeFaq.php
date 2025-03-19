<?php

namespace App\Models\PmsModels\Grn;

use Illuminate\Database\Eloquent\Model;

class ReturnChangeFaq extends Model
{
	protected $table = 'return_change_faq';
	protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = ['faq_id','goods_received_item_id'];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
