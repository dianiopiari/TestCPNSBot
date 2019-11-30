<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHighscoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('highscore', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('chat_id');
			$table->string('name');
            $table->integer('points');
            $table->integer('correct_answers');
            $table->integer('tries');
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('highscore');
    }
}
