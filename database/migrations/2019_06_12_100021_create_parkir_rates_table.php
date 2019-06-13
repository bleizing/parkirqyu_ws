<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParkirRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parkir_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('is_active')->default(1);
            $table->integer('satu_jam_pertama');
            $table->integer('tiap_jam');
            $table->integer('per_hari');
            $table->tinyInteger('parkir_type');     // 1 = Motor, 2 = Mobil
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
        Schema::dropIfExists('parkir_rates');
    }
}
