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

        // 2. Klant
        Schema::create('Klant', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->unsignedInteger('GebruikerId');
            $table->string('Naam', 150);
            $table->string('Telefoonnummer', 20);
            $table->string('WensenAllergieen', 100)->nullable();
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
            $table->foreign('GebruikerId')->references('Id')->on('Gebruiker')->onDelete('cascade');
        });

        // 3. Adres
        Schema::create('Adres', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->unsignedInteger('KlantId');
            $table->string('Straatnaam', 100);
            $table->smallInteger('Huisnummer');
            $table->string('Toevoeging', 10)->nullable();
            $table->string('Postcode', 10);
            $table->string('Plaats', 100);
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
            $table->foreign('KlantId')->references('Id')->on('Klant')->onDelete('cascade');
        });

        // 4. Categorie
        Schema::create('Categorie', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->string('Naam', 150)->unique();
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
        });

        // 5. Leverancier
        Schema::create('Leverancier', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->string('Naam', 100);
            $table->string('Telefoonnummer', 20);
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
        });

        // 6. Product
        Schema::create('Product', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->string('Productnaam', 100);
            $table->string('EanCode', 20)->unique();
            $table->unsignedInteger('CategorieId');
            $table->decimal('Prijs', 6, 2);
            $table->smallInteger('Voorraad');
            $table->smallInteger('MinimumVoorraad');
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
            $table->foreign('CategorieId')->references('Id')->on('Categorie')->onDelete('cascade');
        });

        // 7. ProductPerLeverancier
        Schema::create('ProductPerLeverancier', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->unsignedInteger('ProductId');
            $table->unsignedInteger('LeverancierId');
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
            $table->foreign('ProductId')->references('Id')->on('Product')->onDelete('cascade');
            $table->foreign('LeverancierId')->references('Id')->on('Leverancier')->onDelete('cascade');
        });

        // 8. Behandeling
        Schema::create('Behandeling', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->string('Naam', 100);
            $table->decimal('Prijs', 6, 2);
            $table->smallInteger('DuurMinuten');
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
        });

        // 9. BehandelingPerProduct
        Schema::create('BehandelingPerProduct', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->unsignedInteger('BehandelingId');
            $table->unsignedInteger('ProductId');
            $table->smallInteger('Aantal');
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
            $table->foreign('BehandelingId')->references('Id')->on('Behandeling')->onDelete('cascade');
            $table->foreign('ProductId')->references('Id')->on('Product')->onDelete('cascade');
        });

        // 10. Medewerker
        Schema::create('Medewerker', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->unsignedInteger('GebruikerId');
            $table->unsignedInteger('AdresId');
            $table->string('Naam', 100);
            $table->string('Telefoonnummer', 20);
            $table->string('Specialisaties', 50);
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
            $table->foreign('GebruikerId')->references('Id')->on('Gebruiker')->onDelete('cascade');
            $table->foreign('AdresId')->references('Id')->on('Adres')->onDelete('cascade');
        });

        // 11. MedewerkerPerBehandeling
        Schema::create('MedewerkerPerBehandeling', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->unsignedInteger('MedewerkerId');
            $table->unsignedInteger('BehandelingId');
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
            $table->foreign('MedewerkerId')->references('Id')->on('Medewerker')->onDelete('cascade');
            $table->foreign('BehandelingId')->references('Id')->on('Behandeling')->onDelete('cascade');
        });

        // 12. Werktijd
        Schema::create('Werktijd', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->unsignedInteger('MedewerkerId');
            $table->string('Dag', 10);
            $table->time('Starttijd');
            $table->time('Eindtijd');
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
            $table->foreign('MedewerkerId')->references('Id')->on('Medewerker')->onDelete('cascade');
        });

        // 13. Afspraak
        Schema::create('Afspraak', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->unsignedInteger('KlantId');
            $table->unsignedInteger('MedewerkerId');
            $table->unsignedInteger('BehandelingId');
            $table->date('Datum');
            $table->time('Starttijd');
            $table->string('Status', 20);
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
            $table->foreign('KlantId')->references('Id')->on('Klant')->onDelete('cascade');
            $table->foreign('MedewerkerId')->references('Id')->on('Medewerker')->onDelete('cascade');
            $table->foreign('BehandelingId')->references('Id')->on('Behandeling')->onDelete('cascade');
        });

        // 14. Bestelling
        Schema::create('Bestelling', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->unsignedInteger('ProductId');
            $table->unsignedInteger('KlantId');
            $table->date('Orderdatum');
            $table->date('VerwachteLeverdatum')->nullable();
            $table->string('Status', 30);
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
            $table->foreign('ProductId')->references('Id')->on('Product')->onDelete('cascade');
            $table->foreign('KlantId')->references('Id')->on('Klant')->onDelete('cascade');
        });

        // 15. Bestelregel
        Schema::create('Bestelregel', function (Blueprint $table) {
            $table->unsignedInteger('Id')->primary()->autoIncrement();
            $table->unsignedInteger('BestellingId');
            $table->integer('Aantal');
            $table->decimal('PrijsPerStuk', 6, 2);
            $table->tinyInteger('IsActief');
            $table->string('Opmerking', 255)->nullable();
            $table->dateTime('DatumAangemaakt');
            $table->dateTime('DatumGewijzigd');
            $table->foreign('BestellingId')->references('Id')->on('Bestelling')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('RolPerGebruiker');
        Schema::dropIfExists('Rol');
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
        Schema::dropIfExists('Gebruiker');
    }
};
