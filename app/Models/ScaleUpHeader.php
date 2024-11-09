<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScaleUpHeader extends Model
{
    use HasFactory;
    protected $table = "scaleup_header";
    protected $guarded = ['id'];
    public $timestamps = false;
}
