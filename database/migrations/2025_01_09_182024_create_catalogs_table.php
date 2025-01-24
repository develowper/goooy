<?php

use App\Http\Helpers\Variable;
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
        Schema::create('catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('name_fa', 200)->nullable()->index();
            $table->string('name_en', 200)->nullable()->index();
            $table->string('pn', 30)->nullable()->index();
            $table->unsignedInteger('price')->nullable()->default(null);
            $table->string('image_url', 250)->nullable();
            $table->string('image_indicator', 3)->nullable();
            $table->unsignedInteger('in_shop')->default(0);
            $table->unsignedInteger('in_repo')->default(0);
            $table->enum('status', array_column(Variable::STATUSES, 'name'))->default('active');

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
        Schema::dropIfExists('catalogs');
    }
};
