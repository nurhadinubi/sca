<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChooseMenu extends Model
{
    use HasFactory;
    protected $table = 'choose_menu';
    protected $guarded = ['id'];
    public $timestamps = false;
}
