<?php

namespace App;

use BotMan\BotMan\Interfaces\UserInterface;
use Illuminate\Database\Eloquent\Model;

class Highscore extends Model
{
    protected $fillable = ['tipe_id', 'chat_id', 'name', 'points', 'correct_answers', 'tries', 'rank'];
    protected $table = 'highscore';

    public static function saveUser(UserInterface $botUser, int $userPoints, int $userCorrectAnswers, int $tipeQuestion)
    {
        //$rank = Highscore::getRank();
        $user = static::updateOrCreate(['chat_id' => $botUser->getId()], [
            'chat_id' => $botUser->getId(),
            'name' => $botUser->getFirstName() . ' ' . $botUser->getLastName(),
            'points' => $userPoints,
            'tipe_id' => $tipeQuestion,
            'correct_answers' => $userCorrectAnswers,
        ]);

        $user->increment('tries');

        $user->save();

        return $user;
    }

    public function getRank($tipeQuestion)
    {
        return static::query()->where('points', '>', $this->points)->where('tipe_id',$tipeQuestion)->pluck('points')->unique()->count() + 1;
    }

    public static function topUsers()
    {
        return static::query()->orderByDesc('points')->take(10)->get();
    }

    public static function deleteUser(string $chatId)
    {
        Highscore::where('chat_id', $chatId)->delete();
    }
}
