<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_wallet_id')->constrained('provider_wallets')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['recharge', 'commission_deduction', 'refund']);
            $table->string('notes')->nullable();
            $table->enum('payment_method', ['cash',])->default('cash');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
