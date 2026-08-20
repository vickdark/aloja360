<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rate_periods', function (Blueprint $table) {
            $table->string('accommodation_adjustment_type')->nullable()->after('child_adjustment_value');
            $table->decimal('accommodation_adjustment_value', 14, 2)->nullable()->after('accommodation_adjustment_type');
        });
    }

    public function down(): void
    {
        Schema::table('rate_periods', function (Blueprint $table) {
            $table->dropColumn(['accommodation_adjustment_type', 'accommodation_adjustment_value']);
        });
    }
};
