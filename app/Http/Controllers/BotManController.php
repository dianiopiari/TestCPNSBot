<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\ExampleConversation;
use App\Conversations\HighscoreConversation;
use App\Conversations\PrivacyConversation;
use App\Conversations\QuizConversation;
use App\Conversations\WelcomeConversation;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }

    public function startquiz(BotMan $bot)
    {
        $bot->startConversation(new QuizConversation());
    }

    public function highScore(BotMan $bot)
    {
        $bot->startConversation(new HighscoreConversation());
    }

    public function start(BotMan $bot)
    {
        $bot->startConversation(new WelcomeConversation());
    }

    public function delete(BotMan $bot)
    {
        $bot->startConversation(new PrivacyConversation());
    }
}
