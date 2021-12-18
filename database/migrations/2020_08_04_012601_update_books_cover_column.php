<?php

use App\Book;
use App\Facades\Cover;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class UpdateBooksCoverColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();

        // Update books to use new notation
        Book::unsetEventDispatcher();

        foreach (Book::all() as $book) {
            if (Storage::disk('covers')->exists($book->id . '.png')) {
                Storage::disk('covers')->move($book->id . '.png', $book->hash . '.png');
            }

            $book->cover = Storage::disk('covers')->exists(Cover::getFilename($book));
            $book->save();
        }

        // Then update the table
        Schema::table('books', function (Blueprint $table) {
            $table->boolean('cover')->default(false)->change();
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->binary('cover')->change();
        });
    }
}
