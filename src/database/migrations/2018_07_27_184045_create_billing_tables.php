<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_billing_corp_bill', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('corporation_id');
            $table->smallInteger('month');
            $table->smallInteger('year');
            $table->bigInteger('pve_bill');
            $table->bigInteger('mining_bill');
            $table->smallInteger('pve_taxrate');
            $table->smallInteger('mining_taxrate');
            $table->smallInteger('mining_modifier');
            $table->timestamps();
        });

        Schema::create('seat_billing_character_bill', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id');
            $table->bigInteger('corporation_id');
            $table->smallInteger('month');
            $table->smallInteger('year');
            $table->bigInteger('mining_bill');
            $table->smallInteger('mining_taxrate');
            $table->smallInteger('mining_modifier');
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
        Schema::dropIfExists('seat_billing_corp_bill');
        Schema::dropIfExists('seat_billing_character_bill');
    }
}

