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
        //$this->say("Hey ".$this->bot->getUser()->getFirstName()." 👋 ");
        $this->askIfReady();
    }

    private function askIfReady()
    {
        $question = Question::create('Bot ini adalah solusi bagi Anda yang ingin belajar untuk Test CPNS ? Silahkan Pilih Materi Test yang Diinginkan ?');
        $tipeQuestions = TipeQuestion::where('status',0)->get();
        foreach ($tipeQuestions as $tipeQuestion) {
            $question->addButton(Button::create($tipeQuestion->tipe)->value($tipeQuestion->id));
        }
        $question->addButton(Button::create("Ah Nanti Aja Test Masih Lama")->value('0'));

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() != '0') {
                //$this->say("Perfect!");
                $this->showInfo($answer->getValue());//$this->bot->startConversation(new QuizConversation());
            }else{
                $this->say("😒");
                $this->say("Jika Berubah Pikiran, Anda dapat memulai kuis kapan saja  dengan mengetik /quiz");
            }
        });
    }

    private function showInfo($tipe)
    {
        $tipeQuestionc =  TipeQuestion::find($tipe);
        $this->quizQuestions = QuestionQuiz::where('tipe_id',$tipe)->limit(20)->get()->shuffle();
        $this->questionCount = $this->quizQuestions->count();
        $this->quizQuestions = $this->quizQuestions->keyBy('id');
        $this->say("Akan Ada ' . $this->questionCount . ' pertanyaan tentang materi ". $tipeQuestionc->tipe ." Setiap jawaban yang benar akan memberi Anda poin dalam jumlah tertentu. Harap jujur dan jangan gunakan bantuan apa pun. Lakukan yang terbaik! 🍀");
        $this->checkForNextQuestion();
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
                $this->say("Maaf, saya tidak mengerti. Silakan pilih tombol yang sudah disediakan.");
                return $this->checkForNextQuestion();
            }

            $this->quizQuestions->forget($questionQuiz->id);

            if ($quizAnswer->correct_one) {
                $this->userPoints += $questionQuiz->points;
                $this->userCorrectAnswers++;
                $answerResult = '✅';
            } else {
                $correctAnswer = $questionQuiz->answers()->where('correct_one', true)->first()->text;
                $answerResult = "❌ (Pilihan yang Benar: {$correctAnswer})";
            }
            $this->currentQuestion++;

            $this->say("Jawaban Anda: {$quizAnswer->text} {$answerResult}");
            $this->checkForNextQuestion();
        });
    }

    private function createQuestionTemplate(QuestionQuiz $questionQuiz)
    {
        $questionText = '➡️ Pertanyaan : '.$this->currentQuestion.' / '.$this->questionCount.' : '.$questionQuiz->text;
        $questionTemplate = Question::create($questionText);
        $answers = $questionQuiz->answers->shuffle();

        foreach ($answers as $answer) {
            $questionTemplate->addButton(Button::create($answer->text)->value($answer->id));
        }

        return $questionTemplate;
    }

    private function showResult()
    {
        $this->say("Selesai 🏁");
        $this->say("Anda berhasil melewati semua pertanyaan. Kamu mendapatkan {$this->userPoints} poin! Jawban Benar: {$this->userCorrectAnswers} / {$this->questionCount}");

        $this->askAboutHighscore(); // this is new in this method
    }

    private function askAboutHighscore()
    {
        $question = Question::create('Apakah Anda ingin ditambahkan ke daftar skor tertinggi? Hanya hasil terbaru Anda yang akan disimpan. Untuk mencapai itu, kami perlu menyimpan nama dan chat id Anda.')
            ->addButtons([
                Button::create('Ashiap')->value('yes'),
                Button::create('Enggak, Makasi')->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            switch ($answer->getValue()) {
                case 'yes':
                    $user = Highscore::saveUser($this->bot->getUser(), $this->userPoints, $this->userCorrectAnswers);
                    $this->say("Selesai. Ranking Anda {$user->getRank()}.");
                    return $this->bot->startConversation(new HighscoreConversation());
                case 'no':
                    return $this->say("Tidak masalah. Anda tidak ditambahkan ke papan skor. Anda masih bisa memberi tahu teman Anda tentang hal itu 😉");
                default:
                    return $this->repeat("Maaf, saya tidak mengerti. Silakan pilih tombol yang sudah disediakan.");
            }
        });
    }
}
