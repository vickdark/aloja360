<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rate_periods', function (Blueprint $table) {
            $table->decimal('price_per_night', 14, 2)->nullable()->change();
            $table->string('adjustment_type')->default('amount')->after('price_per_night');
            $table->decimal('adjustment_value', 14, 2)->nullable()->after('adjustment_type');
        });

        // Las temporadas existentes se interpretan retroactivamente como un adicional
        // sobre el precio base del alojamiento (semántica de monto fijo).
        DB::table('rate_periods')
            ->whereNull('adjustment_value')
            ->update([
                'adjustment_type' => 'amount',
                'adjustment_value' => DB::raw('price_per_night'),
            ]);
    }

    public function down(): void
    {
        Schema::table('rate_periods', function (Blueprint $table) {
            $table->dropColumn(['adjustment_type', 'adjustment_value']);
        });
    }
};
