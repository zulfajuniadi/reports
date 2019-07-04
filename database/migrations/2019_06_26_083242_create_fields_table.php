<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('datagrid_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('sys_name');
            $table->string('name');
            $table->boolean('is_sortable')->default(true);
            $table->boolean('is_shown')->default(true);
            $table->string('data_type')->nullable();
            $table->boolean('has_filter')->default(false);
            $table->string('filter_name')->nullable();
            $table->string('filter_type')->nullable()->default('Search');
            $table->timestamps();

            $table->foreign('datagrid_id')
                ->references('id')
                ->on('datagrids')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fields');
    }
}
