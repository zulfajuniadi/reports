<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultFilteredToFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->boolean('has_default_filter')->default(false)->after('filter_type');
            $table->string('default_filter_value')->nullable()->after('has_default_filter');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn('has_default_filter');
        });
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn('default_filter_value');
        });
    }
}
