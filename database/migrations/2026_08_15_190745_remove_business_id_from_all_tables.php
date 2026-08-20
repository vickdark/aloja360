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
        $tables = [
            'accommodations',
            'amenities',
            'rate_periods',
            'blocked_periods',
            'guests',
            'services',
            'quotes',
            'reservations',
            'payments',
            'stays',
            'cleaning_tasks',
            'maintenance_requests',
            'inventory_categories',
            'inventory_items',
            'inventory_checks',
            'expense_categories',
            'expenses',
            'outbound_messages',
            'audit_logs'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'business_id')) {
                // Make nullable first for SQLite compatibility
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->unsignedBigInteger('business_id')->nullable()->change();
                    });
                } catch (\Throwable $e) {}

                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropForeign(['business_id']);
                    });
                } catch (\Throwable $e) {}

                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropColumn('business_id');
                    });
                } catch (\Throwable $e) {}
            }
        }

        if (Schema::hasTable('business_user')) {
            Schema::dropIfExists('business_user');
        }

        if (Schema::hasColumn('users', 'current_business_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['current_business_id']);
                $table->dropColumn('current_business_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this is destructive, usually we wouldn't want to revert removing multitenancy.
    }
};
