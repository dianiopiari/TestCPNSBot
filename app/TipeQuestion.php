<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipeQuestion extends Model
{
    //
    protected $fillable = ['id', 'tipe', 'created_at', 'updated_at'];
    protected $table = 'tipe_questions';
}
