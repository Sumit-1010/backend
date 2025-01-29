<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade'); // This is the foreign key for asset_id
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('opening_balance')->default(0); // This should be a numeric field, not a foreign key
            $table->integer('closing_balance')->default(0);
            $table->enum('transaction_type', ['purchases', 'transfer_in', 'transfer_out']);
            $table->integer('quantity');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
