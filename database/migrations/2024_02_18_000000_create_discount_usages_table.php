<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained();
            $table->foreignId('order_id')->nullable()->constrained();
            $table->foreignId('product_id')->nullable()->constrained();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->decimal('amount_saved', 10, 2);
            $table->decimal('percentage_applied', 5, 2);
            $table->string('type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_usages');
    }
}; 