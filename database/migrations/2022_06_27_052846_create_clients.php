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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rm_id', 25)->constrained('users', 'id');
            $table->string('client_code', 25)->unique();
            $table->string('unique_code', 20)->nullable();
            $table->string('name', 160);
            $table->double('fresh_fund', 12, 2)->default(0);
            $table->double('re_invest', 12, 2)->default(0);
            $table->double('withdrawal', 12, 2)->default(0);
            $table->double('payout', 12, 2)->default(0);
            $table->double('total_aum', 12, 2)->default(0);
            $table->double('other_funds', 12, 2)->default(0);
            $table->double('brokerage', 12, 2)->default(0);
            $table->double('realised_pnl', 12, 2)->default(0);
            $table->string('pfo_type', 20)->nullable();
            $table->string('category', 20)->nullable();
            $table->string('type', 20)->nullable();
            $table->boolean('fno')->nullable();
            $table->date('entry_date')->nullable();
            $table->string('pan_number', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->unsignedBigInteger('family_id')->nullable();
            $table->foreign('family_id')->references('id')->on('client_families')
                ->onUpdate('cascade')->onDelete('set null');
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
        Schema::dropIfExists('clients');
    }
};
