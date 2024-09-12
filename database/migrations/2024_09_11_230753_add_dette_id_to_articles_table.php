<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetteIdToArticlesTable extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('dette_id')->nullable()->after('id'); // Ajoute la colonne 'dette_id'
            $table->foreign('dette_id')->references('id')->on('dettes')->onDelete('cascade'); // Crée une clé étrangère vers la table 'dettes'
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['dette_id']); // Supprime la clé étrangère
            $table->dropColumn('dette_id');    // Supprime la colonne 'dette_id'
        });
    }
}
