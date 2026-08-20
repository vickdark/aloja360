<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->decimal('price_per_child', 14, 2)->nullable()->after('price_per_person');
            $table->decimal('day_pass_price_per_child', 14, 2)->nullable()->after('day_pass_price_per_person');
        });

        // Backfill: si price_per_child es null, usar el valor de price_per_person para mantener compatibilidad histórica
        // Se deja nullable para permitir tarifa niño = 0 (gratis) de forma explícita.
        // No forzamos update automático; PricingService hará fallback a price_per_person cuando sea null.
    }

    public function down(): void
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropColumn(['price_per_child', 'day_pass_price_per_child']);
        });
    }
};
