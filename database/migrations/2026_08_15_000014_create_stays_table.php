<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('accommodation_id')->constrained()->onDelete('cascade');
            $table->foreignId('primary_guest_id')->nullable()->constrained('guests')->nullOnDelete();
            $table->timestamp('actual_check_in_at')->nullable();
            $table->timestamp('actual_check_out_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('actual_guests_count')->nullable();
            $table->decimal('extra_charges_total', 14, 2)->default(0);
            $table->decimal('damages_total', 14, 2)->default(0);
            $table->decimal('security_deposit_returned', 14, 2)->default(0);
            $table->decimal('security_deposit_retained', 14, 2)->default(0);
            $table->text('check_in_notes')->nullable();
            $table->text('check_out_notes')->nullable();
            $table->text('damages_notes')->nullable();
            $table->json('keys_issued')->nullable();
            $table->json('vehicle_info')->nullable();
            $table->text('special_requests')->nullable();
            $table->text('incidents')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'reservation_id']);
            $table->index(['accommodation_id']);
            $table->index(['actual_check_in_at', 'actual_check_out_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stays');
    }
};
