<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDokumensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId("pengadaan_id")->references("id")->on("pengadaans");
            $table->foreignId("jenis_dokumen_id")->references("id")->on("jenis_dokumens");
            $table->foreignId("status_dokumen_id")->references("id")->on("status_dokumens");
            $table->foreignId("created_by_user_id")->references("id")->on("users");
            $table->foreignId("posisi_user_id")->references("id")->on("users");
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
        Schema::dropIfExists('dokumens');
    }
}
