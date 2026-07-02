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
        Schema::table('Gebruiker', function (Blueprint $table) {
            $table->dateTime('EmailGeverifieerdOp')->nullable()->after('Wachtwoord');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Gebruiker', function (Blueprint $table) {
            $table->dropColumn('EmailGeverifieerdOp');
        });
    }
};
