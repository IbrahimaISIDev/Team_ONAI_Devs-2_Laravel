<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaiementsTable extends Migration
{
    public function up()
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 10, 2);
            $table->unsignedBigInteger('dette_id');
            $table->foreign('dette_id')->references('id')->on('dettes');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paiements');
    }
}
