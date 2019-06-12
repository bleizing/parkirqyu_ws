<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('is_active')->default(1);   // 1 = Aktif, 0 = Tidak Aktif
            $table->bigInteger('user_id')->unsigned();
            $table->string('nomor_registrasi');     // Nomor Kendaraan
            $table->string('nama_pemilik');
            $table->string('alamat');
            $table->string('merk');
            $table->string('type');
            $table->string('jenis');
            $table->string('model');
            $table->string('tahun_pembuatan');
            $table->string('nomor_rangka');
            $table->string('nomor_mesin');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
