<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyConstraints extends Migration
{
    public function up()
    {
        Schema::table('paiements', function (Blueprint $table) {
            // Supprimer l'ancienne contrainte si elle existe
            $table->dropForeign(['dette_id']);

            // Ajouter la contrainte avec suppression en cascade
            $table->foreign('dette_id')
                ->references('id')->on('dettes')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('paiements', function (Blueprint $table) {
            // Supprimer la contrainte ajoutée
            $table->dropForeign(['dette_id']);

            // Recréer la contrainte sans suppression en cascade
            $table->foreign('dette_id')
                ->references('id')->on('dettes');
        });
    }
}
