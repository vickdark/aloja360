<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('status')->default('draft');
            $table->foreignId('accommodation_id')->constrained()->onDelete('cascade');
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date');
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
            $table->json('rate_snapshot')->nullable();
            $table->json('services_snapshot')->nullable();
            $table->text('guest_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reservation_id')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index(['accommodation_id', 'check_in_date', 'check_out_date']);
            $table->index(['guest_id']);
            $table->index(['expires_at']);
            $table->index(['code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
