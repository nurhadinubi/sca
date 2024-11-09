<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMaterial extends Model
{
    use HasFactory;
    protected $table = "master_material";
    protected $guarded = ['id'];
    public $timestamps = false;
}
