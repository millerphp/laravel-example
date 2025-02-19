<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('percentage', 5, 2);
            $table->string('type'); // 'fixed', 'percentage', etc
            $table->morphs('discountable'); // For polymorphic relationship
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Higher priority discounts are applied first
            $table->json('rules')->nullable(); // For storing complex discount rules
            $table->json('stacking_rules')->nullable(); // Define what this discount can stack with
            $table->integer('usage_limit')->nullable(); // Maximum number of times this discount can be used
            $table->integer('usage_count')->default(0);
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->decimal('maximum_discount_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
}; 