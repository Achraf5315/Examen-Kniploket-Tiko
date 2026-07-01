<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Gebruiker', function (Blueprint $table) {
            $table->increments('Id');
            $table->string('Gebruikersnaam', 150);
            $table->string('Email', 255)->unique();
            $table->string('Wachtwoord', 255);
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
        });

        Schema::create('Rol', function (Blueprint $table) {
            $table->increments('Id');
            $table->string('Rolnaam', 30);
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
        });

        Schema::create('RolPerGebruiker', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('GebruikerId');
            $table->unsignedInteger('RolId');
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
            $table->foreign('GebruikerId')->references('Id')->on('Gebruiker');
            $table->foreign('RolId')->references('Id')->on('Rol');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('RolPerGebruiker');
        Schema::dropIfExists('Rol');
        Schema::dropIfExists('Gebruiker');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
