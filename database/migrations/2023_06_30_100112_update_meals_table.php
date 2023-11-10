<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::table('meals', function (Blueprint $table) {
            // Change columns with default values
            $table->double('sales')->unsigned()->default(0)->change();
            $table->integer('sold')->unsigned()->default(0)->change();
            $table->boolean('available')->default(true)->change();
            $table->boolean('deleted')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::table('meals', function (Blueprint $table) {
            //
        });
    }
};