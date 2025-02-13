<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('tournament_name')->nullable();
            $table->time('start_time')->nullable();
            $table->integer('game_duration')->nullable();
            $table->integer('break_duration')->nullable();
            $table->integer('additional_slots')->nullable();
            $table->boolean('started_tournament')->default(false);
            $table->boolean('ended_round_games')->default(false);
            $table->boolean('round_robin')->default(true);
            $table->boolean('group_phase')->default(false);
            $table->boolean('play_for_third_place')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('options');
    }
}
