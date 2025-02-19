<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->foreignId('warehouse_id')->constrained()->onDelete('restrict');
            $table->string('reference')->unique();
            $table->enum('type', ['receive', 'ship', 'transfer', 'adjust', 'return']);
            $table->integer('quantity');
            $table->integer('previous_quantity');
            $table->integer('new_quantity');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
}; 