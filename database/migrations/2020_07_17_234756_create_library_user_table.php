<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLibraryUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('library_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('library_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['owner', 'lender'])->default('lender');

            $table->unique(['library_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('library_user');
    }
}
