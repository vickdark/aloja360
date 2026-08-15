<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('document_type')->nullable();
            $table->string('document_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('nationality')->nullable();
            $table->string('occupation')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('marketing_consent')->default(false);
            $table->json('preferences')->nullable();
            $table->integer('total_stays')->default(0);
            $table->integer('total_nights')->default(0);
            $table->decimal('lifetime_value', 14, 2)->default(0);
            $table->timestamp('last_stay_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'document_number']);
            $table->index(['business_id', 'email']);
            $table->index(['business_id', 'phone']);
            $table->index(['last_name', 'first_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
