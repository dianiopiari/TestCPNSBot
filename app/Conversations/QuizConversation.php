<?php

namespace App\Conversations;

use App\AnswerQuiz;
use App\Highscore;
use App\QuestionQuiz;
use App\TipeQuestion;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class QuizConversation extends Conversation
{


    /**
     * Start the conversation.
     *
     * @return mixed
     */
    /** @var Question */
    protected $quizQuestions;

    /** @var integer */
    protected $userPoints = 0;

    /** @var integer */
    protected $userCorrectAnswers = 0;

    /** @var integer */
    protected $questionCount = 0; // we already had this one

    /** @var integer */
    protected $currentQuestion = 1;

    public function run()
    {
        //$this->showInfo();
        // $this->quizQuestions = QuestionQuiz::all()->shuffle();
        // $this->questionCount = $this->quizQuestions->count();
        // $this->quizQuestions = $this->quizQuestions->keyBy('id');
        // $this->showInfo();
        $this->say("Hey ".$this->bot->getUser()->getFirstName()." 👋 ");
        $this->askIfReady();
    }

    private function askIfReady()
    {
        $question = Question::create('Selamat Datang di Test CPNS Bot! Bot ini adalah solusi bagi Anda yang ingin belajar untuk Test CPNS ? Silahkan Pilih Test yang Diinginkan ?');
        $tipeQuestions = TipeQuestion::all();
        $question->addButton(Button::create("No")->value(0));
        foreach ($tipeQuestions as $tipeQuestion) {
            $question->addButton(Button::create($tipeQuestion->tipe)->value($tipeQuestion->id));
        }

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() > '0') {
                $this->say("Perfect!");
                $this->showInfo($answer->getValue());//$this->bot->startConversation(new QuizConversation());
            }else{
                $this->say("😒");
                $this->say("If you change your opinion, you can start the quiz at any time using the start command or by typing /quiz");
            }
        });
    }

    private function showInfo($tipe)
    {
        $tipeQuestionc =  TipeQuestion::find($tipe);
        $this->say("hai ".$tipeQuestionc->tipe());
        // $this->quizQuestions = QuestionQuiz::where('tipe_id','=',$tipe)->shuffle();
        // $this->questionCount = $this->quizQuestions->count();
        // $this->quizQuestions = $this->quizQuestions->keyBy('id');
        // $this->say("You will be shown ' . $this->questionCount . ' questions about '". $tipeQuestionc->tipe() ."'. Setiap jawaban yang benar akan memberi Anda poin dalam jumlah tertentu. Harap jujur dan jangan gunakan bantuan apa pun. Lakukan yang terbaik! 🍀");
        // $this->checkForNextQuestion();
    }

    private function checkForNextQuestion()
    {
        if ($this->quizQuestions->count()) {
            return $this->askQuestion($this->quizQuestions->first());
        }

        $this->showResult();
    }

    private function askQuestion(QuestionQuiz $questionQuiz)
    {
        $this->ask($this->createQuestionTemplate($questionQuiz), function (Answer $answer) use ($questionQuiz) {
            $quizAnswer = AnswerQuiz::find($answer->getValue());
            if (!$quizAnswer) {
                $this->say("Sorry, I did not get that. Please use the buttons.");
                return $this->checkForNextQuestion();
            }

            $this->quizQuestions->forget($questionQuiz->id);

            if ($quizAnswer->correct_one) {
                $this->userPoints += $questionQuiz->points;
                $this->userCorrectAnswers++;
                $answerResult = '✅';
            } else {
                $correctAnswer = $questionQuiz->answers()->where('correct_one', true)->first()->text;
                $answerResult = "❌ (Correct: {$correctAnswer})";
            }
            $this->currentQuestion++;

            $this->say("Your answer: {$quizAnswer->text} {$answerResult}");
            $this->checkForNextQuestion();
        });
    }

    private function createQuestionTemplate(QuestionQuiz $questionQuiz)
    {
        $questionText = '➡️ Question: '.$this->currentQuestion.' / '.$this->questionCount.' : '.$questionQuiz->text;
        $questionTemplate = Question::create($questionText);
        $answers = $questionQuiz->answers->shuffle();

        foreach ($answers as $answer) {
            $questionTemplate->addButton(Button::create($answer->text)->value($answer->id));
        }

        return $questionTemplate;
    }

    private function showResult()
    {
        $this->say("Finished 🏁");
        $this->say("You made it through all the questions. You reached {$this->userPoints} points! Correct answers: {$this->userCorrectAnswers} / {$this->questionCount}");

        $this->askAboutHighscore(); // this is new in this method
    }

    private function askAboutHighscore()
    {
        $question = Question::create('Do you want to get added to the highscore list? Only your latest result will be saved. To achieve that, we need to store your name and chat id.')
            ->addButtons([
                Button::create('Yes please')->value('yes'),
                Button::create('No')->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            switch ($answer->getValue()) {
                case 'yes':
                    $user = Highscore::saveUser($this->bot->getUser(), $this->userPoints, $this->userCorrectAnswers);
                    $this->say("Done. Your rank is {$user->getRank()}.");
                    return $this->bot->startConversation(new HighscoreConversation());
                case 'no':
                    return $this->say("Not problem. You were not added to the highscore. Still you can tell your friends about it 😉");
                default:
                    return $this->repeat("Sorry, I did not get that. Please use the buttons.");
            }
        });
    }
}
