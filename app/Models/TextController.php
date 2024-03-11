<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextController extends Model
{
    use HasFactory;
    protected  $table = 'texts';
    protected $guarded = [];
}
