<?php

use App\Book;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->string('hash')->nullable();
        });

        // Seed values before enabling the unique constraint to avoid errors
        Book::unsetEventDispatcher();

        foreach (Book::all() as $book) {
            $book->hash = uniqid();
            $book->save();
        }
        DB::commit();

        Schema::table('books', function (Blueprint $table) {
            $table->string('hash')->nullable(false)->unique()->change();
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
