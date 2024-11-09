<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomApproval extends Model
{
    use HasFactory;
    protected $table = "bom_approvals";
    protected $guarded = ['id'];
    public $timestamps = false;
}
