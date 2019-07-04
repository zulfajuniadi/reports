<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatagridsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datagrids', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('view_name');
            $table->string('name');
            $table->boolean('is_enabled')->default(true);
            $table->unsignedBigInteger('sort_field_id')->nullable();
            $table->enum('sort_direction', ['asc', 'desc'])->default('asc');
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
        Schema::dropIfExists('datagrids');
    }
}
