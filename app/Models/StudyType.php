<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyType extends Model
{
    protected $fillable = [
        'key','label','description','default_unit','default_qty','default_unit_price'
    ];
}
