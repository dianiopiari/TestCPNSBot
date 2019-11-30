<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnswerQuiz extends Model
{
    //
    protected $fillable = ['id', 'question_id', 'text','correct_one', 'created_at', 'updated_at'];
    protected $table = 'answers';
}
