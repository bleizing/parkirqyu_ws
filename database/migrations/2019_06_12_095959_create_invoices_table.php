<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('is_active')->default(1);   // 1 = Aktif, 0 = Tidak Aktif
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('vehicle_id')->unsigned()->nullable();
            $table->tinyInteger('invoice_type');        // 1 = Parkir, 2 = Topup
            $table->string('kode_referensi')->nullable();
            $table->integer('nominal')->nullable();
            $table->timestamps();       // Created_at = Start, Updated_at = Finish

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
