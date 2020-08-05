<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksHashColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();
        Schema::table('books', function (Blueprint $table) {
            $table->string('hash');
        });

        // Seed values before enabling the unique constraint to avoid errors
        \App\Book::unsetEventDispatcher();

        foreach (\App\Book::all() as $book) {
            $book->hash = uniqid();
            $book->save();
        }
        DB::commit();

        Schema::table('books', function (Blueprint $table) {
            $table->string('hash')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
    }
}
