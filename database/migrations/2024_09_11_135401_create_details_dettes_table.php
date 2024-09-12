<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailsDettesTable extends Migration
{
    public function up()
    {
        Schema::create('details_dettes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dette_id');
            $table->foreign('dette_id')->references('id')->on('dettes');
            $table->unsignedBigInteger('article_id');
            $table->foreign('article_id')->references('id')->on('articles');
            $table->decimal('prix', 10, 2);
            $table->integer('quantite');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('details_dettes');
    }
}