<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class WelcomeConversation extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->welcomeUser();
    }

    private function welcomeUser()
    {
        $this->say("Hey ".$this->bot->getUser()->getFirstName()."");
        $this->askIfReady();
    }

    private function askIfReady()
    {
	$question = Question::create('~ Test CPNS ChatBot ~ Are you ready for the quiz?')
		->addButtons([
			Button::create('Ashiap...!')->value('yes'),
			Button::create('Enggak Ah, Males')->value('no'),
		]);

	$this->ask($question, function (Answer $answer) {
		if ($answer->getValue() == 'yes') {
			$this->say("Perfect!");
			return $this->bot->startConversation(new QuizConversation());
		}else{
            $this->say(":(");
		    $this->say("Jika Berubah Pikiran, Anda dapat memulai kuis kapan saja  dengan mengetik /quiz");
        }


	});
    }
}