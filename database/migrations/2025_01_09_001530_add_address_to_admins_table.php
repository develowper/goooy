<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('postal_code', 20)->nullable()->after('ref_id');
            $table->unsignedSmallInteger('province_id')->nullable()->index()->after('postal_code');
            $table->foreign('province_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('county_id')->nullable()->after('province_id');
            $table->foreign('county_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('district_id')->nullable()->after('county_id');
            $table->foreign('district_id')->references('id')->on('cities')->onDelete('no action');

            $table->string('address', 2048)->nullable()->after('district_id');
            $table->string('location', 50)->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('postal_code');
            $table->dropColumn('province_id');
            $table->dropColumn('county_id');
            $table->dropColumn('district_id');
            $table->dropColumn('address');
            $table->dropColumn('location');
        });
    }
};
