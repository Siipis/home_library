<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class createCategoriesHashColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();
        Schema::table('categories', function (Blueprint $table) {
            $table->string('hash');
        });

        foreach (\App\Category::all() as $category) {
            $category->hash = uniqid();
            $category->save();
        }

        DB::commit();

        Schema::table('categories', function (Blueprint $table) {
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
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
    }
}
