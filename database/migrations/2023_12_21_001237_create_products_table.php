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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->json('categories')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('no action');
            $table->string('tags', 1024)->nullable();
            $table->enum('status', array_column(Variable::STATUSES, 'name'))->default('inactive');
            $table->unsignedBigInteger('sell_count')->default(0);
//            $table->unsignedDecimal('in_shop', 15, 3)->default(0); //weight|count

            $table->timestamp('charged_at')->nullable();
            $table->unsignedInteger('rate')->nullable();
            $table->timestamps();
            $table->longText('description')->nullable();
//            $table->unsignedDecimal('in_repo', 8, 3)->default(0); //weight|count
//            $table->unsignedDecimal('in_shop', 8, 3)->default(0); //weight|count
//            $table->unsignedInteger('price')->default(0);
//            $table->unsignedInteger('auction_price')->nullable();
//            $table->unsignedBigInteger('repo_id')->nullable();
//            $table->foreign('repo_id')->references('id')->on('repositories')->onDelete('no action');
//            $table->boolean('in_auction')->default(false);
            $table->decimal('weight', 8, 3)->unsigned()->nullable(); //kg

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
