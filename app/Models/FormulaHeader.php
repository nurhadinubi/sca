<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulaHeader extends Model
{
    use HasFactory;
    protected $table = "formula_header";
    protected $guarded = ['id'];
    public $timestamps = false;
}
