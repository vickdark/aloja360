<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('accommodation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('reported');
            $table->string('priority')->default('medium');
            $table->string('category')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reported_at')->useCurrent();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('estimated_cost', 14, 2)->nullable();
            $table->decimal('actual_cost', 14, 2)->nullable();
            $table->foreignId('blocked_period_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('blocks_accommodation')->default(false);
            $table->text('technician_notes')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->json('photos')->nullable();
            $table->foreignId('expense_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'priority']);
            $table->index(['accommodation_id', 'status']);
            $table->index(['assigned_to']);
            $table->index(['scheduled_at']);
        });

        Schema::table('blocked_periods', function (Blueprint $table) {
            $table->foreign('maintenance_request_id')
                ->references('id')->on('maintenance_requests')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('blocked_periods', function (Blueprint $table) {
            $table->dropForeign(['maintenance_request_id']);
        });

        Schema::dropIfExists('maintenance_requests');
    }
};
