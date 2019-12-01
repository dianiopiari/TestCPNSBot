<?php

namespace App\Conversations;

use App\Highscore;
use BotMan\BotMan\Messages\Conversations\Conversation;

class HighscoreConversation extends Conversation
{
    /**
     * Start the conversation.
     * coba gitnya apa bisa atau enggak
     *
     * @return mixed
     */
    public function run()
    {
        $this->showHighscore();
    }

    private function showHighscore()
    {
        $topUsers = Highscore::topUsers();

        if (!$topUsers->count()) {
            return $this->say("Papan Skor Masih Kosong. Jadilah yang Pertama! 👍");
        }

        $this->say("Berikut adalah skor tertinggi saat ini. Apakah Anda pikir Anda bisa lebih baik? Mulai ikuti kuis: /quiz");
        $this->say("🏆 HIGHSCORE 🏆");
        $tipeQuestions = TipeQuestion::where('status',0)->get();
        foreach ($tipeQuestions as $tipeQuestion) {
            $topUsers = Highscore::topUsersPerTipe($tipeQuestion->id);
            // $topUsers->transform(function ($user) {
            //     return "{{$user->name} {$user->points} points";
            //  });
            $this->say("Skor Tertinggi untuk materi ".$tipeQuestion->tipe);
           // $this->say($topUsers->implode("\n"));
        }

    }
}
