<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('type')->default('payment');
            $table->string('status')->default('pending');
            $table->string('method')->default('cash');
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('currency')->default('COP');
            $table->decimal('exchange_rate', 14, 6)->nullable();
            $table->date('payment_date');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('reference')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('gateway')->nullable();
            $table->text('gateway_response')->nullable();
            $table->text('voucher_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'type']);
            $table->index(['reservation_id', 'status']);
            $table->index(['guest_id']);
            $table->index(['payment_date']);
            $table->index(['code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
