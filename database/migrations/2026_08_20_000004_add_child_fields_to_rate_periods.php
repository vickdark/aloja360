<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rate_periods', function (Blueprint $table) {
            $table->string('child_adjustment_type')->nullable()->after('adjustment_value');
            $table->decimal('child_adjustment_value', 14, 2)->nullable()->after('child_adjustment_type');
            $table->decimal('extra_child_price', 14, 2)->nullable()->after('extra_guest_price');
        });
    }

    public function down(): void
    {
        Schema::table('rate_periods', function (Blueprint $table) {
            $table->dropColumn(['child_adjustment_type', 'child_adjustment_value', 'extra_child_price']);
        });
    }
};
