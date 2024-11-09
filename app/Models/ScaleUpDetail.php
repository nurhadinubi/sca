<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScaleUpDetail extends Model
{
    use HasFactory;
    protected $table = "scaleup_detail";

    protected $guarded = ['id'];
    public $timestamps = false;
}
