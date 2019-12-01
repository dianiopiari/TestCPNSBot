<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionQuiz extends Model
{
    //
    protected $fillable = ['id','tipe_id', 'text', 'points', 'created_at', 'updated_at'];
    protected $table = 'questions';

	public function answers()
    {
        return $this->hasMany(AnswerQuiz::class,'question_id','id');
    }

    public function tipe()
    {
        return $this->hasOne(TipeQuestion::class,'id','tipe_id');
    }
}
