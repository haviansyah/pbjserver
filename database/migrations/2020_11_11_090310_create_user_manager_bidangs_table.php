<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserManagerBidangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_manager_bidangs', function (Blueprint $table) {
            $table->foreignId("user_id")->references("id")->on("users");
            $table->foreignId("bidang_id")->references("id")->on("direksi_pengadaans");
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
        Schema::dropIfExists('user_manager_bidangs');
    }
}
