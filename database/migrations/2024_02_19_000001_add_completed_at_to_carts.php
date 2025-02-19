<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            
            // Remove the status column if we're using completed_at instead
            $table->dropColumn('status');
        });
    }

    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'total_amount']);
            $table->string('status')->nullable();
        });
    }
}; 