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
        Schema::create('rubik', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('face_id');
            $table->string('lang', '2')->nullable();
            $table->string('title', '100')->nullable();
            $table->string('icon', '1024')->nullable();
            $table->string('link', '1024')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('rubik')->insert(\App\Http\Helpers\Variable::getRubikFaces());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rubik');
    }
};
