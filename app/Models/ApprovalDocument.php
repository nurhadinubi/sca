<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalDocument extends Model
{
    use HasFactory;
    protected $table = "approval_document";
    protected $guarded = ['id'];
    public $timestamps = false;
}
