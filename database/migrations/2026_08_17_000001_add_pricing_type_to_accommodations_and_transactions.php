<?php

use App\Enums\PricingType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->string('pricing_type')->default(PricingType::PerAccommodation->value)->after('base_price');
            $table->decimal('price_per_person', 14, 2)->default(0)->after('pricing_type');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->string('pricing_type')->default(PricingType::PerAccommodation->value)->after('accommodation_id');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->string('pricing_type')->default(PricingType::PerAccommodation->value)->after('accommodation_id');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['pricing_type']);
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['pricing_type']);
        });

        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropColumn(['pricing_type', 'price_per_person']);
        });
    }
};
