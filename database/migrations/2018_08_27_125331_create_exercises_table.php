<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * [CreateExercisesTable description]
 */
class CreateExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->increments('id');
            $table->string('exercise');
            $table->timestamps();
        });

        Schema::create('stages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('stage');
            $table->timestamps();
        });

        Schema::create('exercise_stages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('exercise_id');
            $table->unsignedInteger('stage_id');
            $table->string('weight')->nullable();
            $table->integer('time')->nullable();
            $table->integer('repetitions')->nullable();
            $table->integer('round')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            $table->foreign('stage_id')->references('id')->on('stages')->onDelete('cascade');
        });

        Schema::create('clase_exercise_stages', function (Blueprint $table) {
          $table->unsignedInteger('clase_id');
          $table->unsignedInteger('exercise_stage_id');
          $table->timestamps();

          $table->foreign('clase_id')->references('id')->on('clases')->onDelete('cascade');
          $table->foreign('exercise_stage_id')->references('id')->on('exercise_stages')->onDelete('cascade');
        });

        Schema::create('statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('statistic');
            $table->timestamps();
        });

        Schema::create('reservation_statistic_stages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('statistic_id');
            $table->unsignedInteger('reservation_id');
            $table->unsignedInteger('exercise_stage_id');
            $table->string('weight')->nullable();
            $table->integer('time')->nullable();
            $table->integer('repetitions')->nullable();
            $table->integer('round')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('statistic_id')->references('id')->on('statistics')->onDelete('cascade');
            $table->foreign('exercise_stage_id')->references('id')->on('exercise_stages')->onDelete('cascade');
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::disableForeignKeyConstraints();
      Schema::dropIfExists('exercises');
      Schema::dropIfExists('stages');
      Schema::dropIfExists('clase_exercise_stages');
      Schema::dropIfExists('exercise_stages');
      Schema::dropIfExists('statistics');
      Schema::dropIfExists('reservation_statistic_stages');
    }
}
