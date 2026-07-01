<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Klant', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('GebruikerId');
            $table->string('Naam', 150);
            $table->string('Telefoonnummer', 20);
            $table->string('WensenAllergieen', 100)->nullable();
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');

            $table->foreign('GebruikerId')->references('Id')->on('Gebruiker');
        });

        Schema::create('Adres', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('KlantId');
            $table->string('Straatnaam', 100);
            $table->smallInteger('Huisnummer');
            $table->string('Toevoeging', 10)->nullable();
            $table->string('Postcode', 10);
            $table->string('Plaats', 100);
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');

            $table->foreign('KlantId')->references('Id')->on('Klant');
        });

        Schema::create('Categorie', function (Blueprint $table) {
            $table->increments('Id');
            $table->string('Naam', 150)->unique();
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
        });

        Schema::create('Leverancier', function (Blueprint $table) {
            $table->increments('Id');
            $table->string('Naam', 100);
            $table->string('Telefoonnummer', 20);
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
        });

        Schema::create('Product', function (Blueprint $table) {
            $table->increments('Id');
            $table->string('Productnaam', 100);
            $table->string('EanCode', 20)->unique();
            $table->unsignedInteger('CategorieId');
            $table->decimal('Prijs', 6, 2);
            $table->smallInteger('Voorraad');
            $table->smallInteger('MinimumVoorraad');
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');

            $table->foreign('CategorieId')->references('Id')->on('Categorie');
        });

        Schema::create('ProductPerLeverancier', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('ProductId');
            $table->unsignedInteger('LeverancierId');
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');

            $table->foreign('ProductId')->references('Id')->on('Product');
            $table->foreign('LeverancierId')->references('Id')->on('Leverancier');
        });

        Schema::create('Behandeling', function (Blueprint $table) {
            $table->increments('Id');
            $table->string('Naam', 100);
            $table->decimal('Prijs', 6, 2);
            $table->smallInteger('DuurMinuten');
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
        });

        Schema::create('BehandelingPerProduct', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('BehandelingId');
            $table->unsignedInteger('ProductId');
            $table->smallInteger('Aantal');
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');

            $table->foreign('BehandelingId')->references('Id')->on('Behandeling');
            $table->foreign('ProductId')->references('Id')->on('Product');
        });

        Schema::create('Medewerker', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('GebruikerId');
            $table->unsignedInteger('AdresId');
            $table->string('Naam', 100);
            $table->string('Telefoonnummer', 20);
            $table->string('Specialisaties', 50);
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');

            $table->foreign('GebruikerId')->references('Id')->on('Gebruiker');
            $table->foreign('AdresId')->references('Id')->on('Adres');
        });

        Schema::create('MedewerkerPerBehandeling', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('MedewerkerId');
            $table->unsignedInteger('BehandelingId');
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');

            $table->foreign('MedewerkerId')->references('Id')->on('Medewerker');
            $table->foreign('BehandelingId')->references('Id')->on('Behandeling');
        });

        Schema::create('Werktijd', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('MedewerkerId');
            $table->string('Dag', 10);
            $table->time('Starttijd');
            $table->time('Eindtijd');
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');

            $table->foreign('MedewerkerId')->references('Id')->on('Medewerker');
        });

        Schema::create('Afspraak', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('KlantId');
            $table->unsignedInteger('MedewerkerId');
            $table->unsignedInteger('BehandelingId');
            $table->date('Datum');
            $table->time('Starttijd');
            $table->string('Status', 20);
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');

            $table->foreign('KlantId')->references('Id')->on('Klant');
            $table->foreign('MedewerkerId')->references('Id')->on('Medewerker');
            $table->foreign('BehandelingId')->references('Id')->on('Behandeling');
        });

        Schema::create('Bestelling', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('ProductId');
            $table->unsignedInteger('KlantId');
            $table->date('Orderdatum');
            $table->date('VerwachteLeverdatum')->nullable();
            $table->string('Status', 30);
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');

            $table->foreign('ProductId')->references('Id')->on('Product');
            $table->foreign('KlantId')->references('Id')->on('Klant');
        });

        Schema::create('Bestelregel', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('BestellingId');
            $table->integer('Aantal');
            $table->decimal('PrijsPerStuk', 6, 2);
            $table->boolean('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');

            $table->foreign('BestellingId')->references('Id')->on('Bestelling');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Bestelregel');
        Schema::dropIfExists('Bestelling');
        Schema::dropIfExists('Afspraak');
        Schema::dropIfExists('Werktijd');
        Schema::dropIfExists('MedewerkerPerBehandeling');
        Schema::dropIfExists('Medewerker');
        Schema::dropIfExists('BehandelingPerProduct');
        Schema::dropIfExists('Behandeling');
        Schema::dropIfExists('ProductPerLeverancier');
        Schema::dropIfExists('Product');
        Schema::dropIfExists('Leverancier');
        Schema::dropIfExists('Categorie');
        Schema::dropIfExists('Adres');
        Schema::dropIfExists('Klant');
    }
};
