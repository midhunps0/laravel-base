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
        Schema::create('clients_scripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients', 'id');
            $table->string('script_id')->constrained('scripts', 'id');
            $table->date('entry_date')->nullable();
            $table->integer('dp_qty');
            $table->integer('available_qty');
            $table->double('buy_avg_price');
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
        Schema::dropIfExists('clients_scripts');
    }
};
