<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('status')->default('pending');
            $table->string('source')->default('manual');
            $table->foreignId('accommodation_id')->constrained()->onDelete('cascade');
            $table->foreignId('primary_guest_id')->nullable()->constrained('guests')->nullOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->string('check_in_time')->nullable();
            $table->string('check_out_time')->nullable();
            $table->timestamp('actual_check_in_at')->nullable();
            $table->timestamp('actual_check_out_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('guests_count')->default(1);
            $table->integer('adults_count')->default(1);
            $table->integer('children_count')->default(0);
            $table->integer('nights_count')->default(1);
            $table->decimal('nightly_subtotal', 14, 2)->default(0);
            $table->decimal('services_total', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('cleaning_fee', 14, 2)->default(0);
            $table->decimal('security_deposit', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('deposit_required', 14, 2)->default(0);
            $table->string('deposit_policy')->nullable();
            $table->json('rate_snapshot')->nullable();
            $table->text('guest_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('no_show_at')->nullable();
            $table->text('no_show_reason')->nullable();
            $table->foreignId('no_show_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hold_until')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index(['accommodation_id', 'check_in_date', 'check_out_date']);
            $table->index(['primary_guest_id']);
            $table->index(['check_in_date']);
            $table->index(['check_out_date']);
            $table->index(['code']);
            $table->index(['created_at']);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->foreign('reservation_id')
                ->references('id')->on('reservations')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
        });

        Schema::dropIfExists('reservations');
    }
};
