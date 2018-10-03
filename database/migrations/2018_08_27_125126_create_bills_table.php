<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * [CreateBillsTable description]
 */
class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payment_type');
            $table->timestamps();
        });

        Schema::create('bills', function (Blueprint $table) {
          $table->increments('id');
          $table->unsignedInteger('payment_type_id');
          $table->unsignedInteger('user_id');
          $table->string('date');
          $table->string('detail')->nullable();
          $table->integer('amount');
          $table->integer('subtotal')->nullable();
          $table->integer('total');
          $table->timestamps();
          $table->softDeletes();

          $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('cascade');
          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('payment_statuses', function (Blueprint $table) {
          $table->increments('id');
          $table->string('payment_status');
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('payment_types');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('payment_statuses');
    }
}
