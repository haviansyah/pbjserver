<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengadaansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengadaans', function (Blueprint $table) {
            $table->id();
            $table->string("judul_pengadaan", 100);
            $table->string("nomor_kontrak")->nullable();

            $table->foreignId('status_pengadaan_id');
            $table->foreignId('jenis_pengadaan_id');
            $table->foreignId('direksi_pengadaan_id');
            $table->foreignId('jenis_anggaran_id');
            $table->foreignId('created_by_user_id');

            $table->foreign('status_pengadaan_id')->references('id')->on('status_pengadaans');
            $table->foreign('jenis_pengadaan_id')->references('id')->on('jenis_pengadaans');
            $table->foreign('direksi_pengadaan_id')->references('id')->on('direksi_pengadaans');
            $table->foreign('jenis_anggaran_id')->references('id')->on('jenis_anggarans');
            $table->foreign('created_by_user_id')->references('id')->on('users');
            

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
        Schema::dropIfExists('pengadaans');
    }
}
