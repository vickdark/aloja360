<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cleaning_tasks', function (Blueprint $table) {
            $table->decimal('cost', 14, 2)->nullable()->after('quality_score');
        });
    }

    public function down(): void
    {
        Schema::table('cleaning_tasks', function (Blueprint $table) {
            $table->dropColumn('cost');
        });
    }
};
