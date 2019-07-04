<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('view_name');
            $table->string('type');
            $table->text('labels')->nullable();
            $table->text('datasets')->nullable();
            $table->text('query')->nullable();
            $table->string('bg_color')->default('#f8f9fa');
            $table->string('label_pos')->default('right');
            $table->string('color_scheme')->default('brewer.RdYlGn11');
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
        Schema::dropIfExists('charts');
    }
}
