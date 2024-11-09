<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeycodeApprovalHeader extends Model
{
    use HasFactory;
    protected $table = "keycode_approval";
    protected $guarded = ['id'];
    public $timestamps = false;
}
