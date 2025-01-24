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
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->enum('status', array_column(Variable::SAMPLE_STATUSES, 'name'))->default(array_column(Variable::SAMPLE_STATUSES, 'name')[0]);
            $table->unsignedBigInteger('agency_id')->nullable();
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('no action');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('no action');
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('no action');
            $table->unsignedTinyInteger('guarantee_months')->nullable();
            $table->timestamp('guarantee_expires_at')->nullable();
            $table->timestamp('produced_at')->nullable();
            $table->string('barcode', 30)->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('no action');
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->foreign('operator_id')->references('id')->on('admins')->onDelete('no action');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('no action');
            $table->timestamps();
            $table->unsignedBigInteger('repo_id')->nullable();
            $table->foreign('repo_id')->references('id')->on('repositories')->onDelete('no action');
            $table->unsignedInteger('price')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('samples');
    }
};
