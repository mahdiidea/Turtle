<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("thread_id");
            $table->unsignedBigInteger("user_id");
            $table->string('body', 10240)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('thread_id')->references('id')->on('threads')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('messages_attaches', function (Blueprint $blueprint) {
            $blueprint->bigIncrements('id');
            $blueprint->unsignedBigInteger('message_id');
            $blueprint->unsignedBigInteger('attach_id');

            $blueprint->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
            $blueprint->foreign('attach_id')->references('id')->on('attaches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages_attaches');
        Schema::dropIfExists('messages');
    }
}
