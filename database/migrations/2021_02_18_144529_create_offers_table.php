<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('market');
            $table->decimal('rate', 18,10);
            $table->decimal('amount', 18,10);
            $table->decimal('price', 18,10);
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->string('offerId')->nullable();
            $table->boolean('completed')->nullable();
            $table->string('errors')->nullable();
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
        Schema::dropIfExists('offers');
    }
}
