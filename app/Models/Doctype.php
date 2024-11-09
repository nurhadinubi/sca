<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctype extends Model
{
    use HasFactory;
    protected $table = 'doctype';
    protected $guarded = ['id'];
    public $timestamps = false;
}
