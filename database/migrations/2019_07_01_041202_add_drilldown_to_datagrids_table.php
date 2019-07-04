<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDrilldownToDatagridsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datagrids', function (Blueprint $table) {
            $table->boolean('has_drilldown')->nullable()->default(false);
            $table->bigInteger('drilldown_report_id')->nullable();
            $table->text('drilldown_filters')->nullable();
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
            $table->dropColumn('has_drilldown');
        });
        Schema::table('datagrids', function (Blueprint $table) {
            $table->dropColumn('drilldown_report_id');
        });
        Schema::table('datagrids', function (Blueprint $table) {
            $table->dropColumn('drilldown_filters');
        });
    }
}
