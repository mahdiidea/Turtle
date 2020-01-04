<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attaches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("user_id");
            $table->string("uri");
            $table->string("mime")->nullable();
            $table->string("name")->nullable();
            $table->string("size");
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('posts_attaches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('attach_id');
            $table->unsignedBigInteger('post_id');

            $table->foreign('attach_id')->references('id')->on('attaches')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts_attaches');
        Schema::dropIfExists('attaches');
    }
}
