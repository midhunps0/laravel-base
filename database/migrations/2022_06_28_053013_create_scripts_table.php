<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scripts', function (Blueprint $table) {
            $table->id();
            $table->string('isin_code', 50);
            $table->string('symbol', 50);
            $table->string('company_name', 50);
            $table->string('industry', 50)->nullable();
            $table->string('series', 50)->nullable();
            $table->boolean('fno')->nullable();
            $table->boolean('nifty')->nullable();
            $table->integer('nse_code')->nullable();
            $table->integer('bse_code')->nullable();
            $table->string('yahoo_code', 50)->nullable();
            $table->string('doc', 20)->nullable();
            $table->string('bbg_ticker', 50)->nullable();
            $table->string('bse_security_id', 50)->nullable();
            $table->integer('capitaline_code');
            $table->string('mvg_sector', 100)->nullable();
            $table->string('agio_indutry', 100)->nullable();
            $table->string('remarks', 200)->nullable();
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
        Schema::dropIfExists('scripts');
    }
};
