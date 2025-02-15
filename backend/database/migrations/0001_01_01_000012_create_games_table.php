<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_a_id')->nullable();
            $table->unsignedBigInteger('team_b_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('hall_id')->nullable();
            $table->unsignedBigInteger('timeslot_id')->nullable();
            $table->integer('finale_type')->nullable();
            $table->boolean('play_for_third')->default(false);
            $table->integer('points_team_a')->default(0);
            $table->integer('points_team_b')->default(0);
            $table->boolean('played')->default(false);
            $table->boolean('temporary')->default(false);
            $table->timestamps();

            $table->foreign('team_a_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('team_b_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('hall_id')->references('id')->on('halls')->onDelete('cascade');
            $table->foreign('timeslot_id')->references('id')->on('timeslots')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('games');
    }
}
