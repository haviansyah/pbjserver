<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityDokumensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId("dokumen_id")->references("id")->on("dokumens");
            $table->foreignId("from_user_id")->references("id")->on("users");
            $table->foreignId("to_user_id")->references("id")->on("users");
            $table->integer("state");
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
        Schema::dropIfExists('activity_dokumens');
    }
}
