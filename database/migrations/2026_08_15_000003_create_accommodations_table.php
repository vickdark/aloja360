<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('type')->default('cabin');
            $table->string('status')->default('available');
            $table->text('description')->nullable();
            $table->integer('max_guests')->default(2);
            $table->integer('min_nights')->default(1);
            $table->integer('max_nights')->nullable();
            $table->integer('bedrooms')->default(0);
            $table->integer('beds')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->decimal('base_price', 14, 2)->default(0);
            $table->decimal('cleaning_fee', 14, 2)->default(0);
            $table->decimal('security_deposit', 14, 2)->default(0);
            $table->decimal('weekend_price_modifier', 14, 2)->nullable();
            $table->string('check_in_time')->default('15:00');
            $table->string('check_out_time')->default('11:00');
            $table->text('house_rules')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'type']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
