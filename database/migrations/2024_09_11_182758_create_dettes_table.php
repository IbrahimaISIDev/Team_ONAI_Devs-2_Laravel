<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDettesTable extends Migration
{
    public function up()
    {
        Schema::create('dettes', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 10, 2);
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dettes');
    }
}