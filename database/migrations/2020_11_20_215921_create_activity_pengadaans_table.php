<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityPengadaansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_pengadaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId("dokumen_id")->references("id")->on("dokumens");
            $table->foreignId("from_user_id")->references("id")->on("users");
            $table->foreignId("to_user_id")->references("id")->on("users");
            $table->foreignId("status_pengadaan_id")->references("id")->on("status_pengadaans");
            $table->string("keterangan")->nullable();
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
        Schema::dropIfExists('activity_pengadaans');
    }
}
