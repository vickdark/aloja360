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
            $table->boolean('allows_day_pass')->default(false)->after('price_per_person');
            $table->integer('day_pass_max_guests')->nullable()->after('allows_day_pass');
            $table->time('day_pass_check_in_time')->default('08:00:00')->nullable()->after('day_pass_max_guests');
            $table->time('day_pass_check_out_time')->default('17:00:00')->nullable()->after('day_pass_check_in_time');
            $table->string('day_pass_pricing_type')->default(PricingType::PerAccommodation->value)->after('day_pass_check_out_time');
            $table->decimal('day_pass_base_price', 14, 2)->nullable()->after('day_pass_pricing_type');
            $table->decimal('day_pass_price_per_person', 14, 2)->nullable()->after('day_pass_base_price');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->boolean('is_day_pass')->default(false)->after('pricing_type');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->boolean('is_day_pass')->default(false)->after('pricing_type');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['is_day_pass']);
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['is_day_pass']);
        });

        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropColumn([
                'allows_day_pass',
                'day_pass_max_guests',
                'day_pass_check_in_time',
                'day_pass_check_out_time',
                'day_pass_pricing_type',
                'day_pass_base_price',
                'day_pass_price_per_person',
            ]);
        });
    }
};
