<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('accommodation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->integer('expected_quantity')->default(1);
            $table->integer('current_quantity')->default(1);
            $table->string('unit')->default('unit');
            $table->decimal('unit_value', 14, 2)->nullable();
            $table->decimal('replacement_cost', 14, 2)->nullable();
            $table->text('location')->nullable();
            $table->string('condition')->default('good');
            $table->date('purchase_date')->nullable();
            $table->date('last_checked_at')->nullable();
            $table->boolean('is_consumable')->default(false);
            $table->integer('reorder_threshold')->nullable();
            $table->json('photos')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'accommodation_id']);
            $table->index(['business_id', 'category']);
            $table->index(['name']);
        });

        Schema::create('inventory_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('accommodation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('stay_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cleaning_task_id')->nullable()->constrained()->nullOnDelete();
            $table->string('check_type')->default('general');
            $table->timestamp('performed_at')->useCurrent();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('total_items')->default(0);
            $table->integer('missing_count')->default(0);
            $table->integer('damaged_count')->default(0);
            $table->decimal('total_charge_amount', 14, 2)->default(0);
            $table->boolean('charge_to_guest')->default(false);
            $table->text('notes')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'accommodation_id']);
            $table->index(['reservation_id']);
            $table->index(['performed_at']);
        });

        Schema::create('inventory_check_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_check_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name');
            $table->integer('expected_quantity')->default(1);
            $table->integer('actual_quantity')->default(0);
            $table->integer('missing_quantity')->default(0);
            $table->integer('damaged_quantity')->default(0);
            $table->string('condition_found')->nullable();
            $table->decimal('charge_amount', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();

            $table->index(['inventory_check_id', 'inventory_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_check_items');
        Schema::dropIfExists('inventory_checks');
        Schema::dropIfExists('inventory_items');
    }
};
