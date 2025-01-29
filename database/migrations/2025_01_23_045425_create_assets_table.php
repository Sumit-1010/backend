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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->foreignId('base_id')->constrained()->onDelete('cascade');
            $table->integer('opening_balance')->default(0); // Opening balance
            $table->integer('purchases')->default(0); // Purchases
            $table->integer('transfers_in')->default(0); // Transfers into the base
            $table->integer('transfers_out')->default(0); // Transfers out of the base
            $table->integer('closing_balance')->default(0); // Closing balance
            $table->integer('net_movements')->default(0); // Net movements (calculated)
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
