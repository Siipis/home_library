<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('local_id')->unique()->nullable();
            $table->string('title');
            $table->string('series')->nullable();
            $table->string('authors')->nullable();
            $table->string('publisher')->nullable();
            $table->text('description')->nullable();
            $table->string('year')->nullable();
            $table->string('language')->nullable();
            $table->text('keywords')->nullable();
            $table->string('isbn')->nullable();
            $table->text('providers')->nullable();
            $table->binary('cover');

            $table->unsignedBigInteger('library_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('library_id')
                ->references('id')->on('libraries')
                ->onDelete('set null');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
