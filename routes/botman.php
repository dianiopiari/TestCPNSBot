<?php

use BotMan\BotMan\BotMan;
use App\Http\Controllers\BotManController;
use App\Http\Middleware\TypingMiddleware;

$botman = resolve("botman");

$botman->hears("Hi", function ($bot) {
    $bot->reply("Hello!");
});
$botman->hears("Start conversation", BotManController::class."@startConversation");
$botman->hears("/quiz|quiz", BotManController::class."@startquiz");
$botman->hears("/highscore|highscore", BotManController::class."@highScore");
$botman->hears("/start|start", BotManController::class."@start");
$botman->hears("/delete|delete", BotManController::class."@delete");
$botman->hears("/about|about", function (BotMan $bot) {
    $bot->reply("Test CPNS Bot is Project With Love By @DianiOpiari");
});

$typingMiddleware = new TypingMiddleware();
$botman->middleware->sending($typingMiddleware);
