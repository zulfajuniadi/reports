<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsSummaryViewToDatagridsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datagrids', function (Blueprint $table) {
            $table->boolean('is_summary')->default(false);
            $table->string('summary_type')->nullable();
            $table->unsignedBigInteger('summary_by_column')->nullable();
            $table->unsignedBigInteger('summary_count_column')->nullable();
            $table->boolean('has_sum_footer')->default(false);
            $table->boolean('has_grand_sum_footer')->default(false);
            $table->boolean('has_sum_row')->default(false);
            $table->string('color_scheme')->default('brewer.RdYlGn11');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('datagrids', function (Blueprint $table) {
            $table->dropColumn('is_summary');
        });
        Schema::table('datagrids', function (Blueprint $table) {
            $table->dropColumn('summary_type');
        });
        Schema::table('datagrids', function (Blueprint $table) {
            $table->dropColumn('summary_by_column');
        });
        Schema::table('datagrids', function (Blueprint $table) {
            $table->dropColumn('summary_count_column');
        });
        Schema::table('datagrids', function (Blueprint $table) {
            $table->dropColumn('has_sum_footer');
        });
        Schema::table('datagrids', function (Blueprint $table) {
            $table->dropColumn('has_grand_sum_footer');
        });
        Schema::table('datagrids', function (Blueprint $table) {
            $table->dropColumn('has_sum_row');
        });
        Schema::table('datagrids', function (Blueprint $table) {
            $table->dropColumn('color_scheme');
        });
    }
}
