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
        Schema::create('security_deposit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('security_deposit_accounts');
            $table->foreignId('contract_id')->nullable();
            $table->bigInteger('amount');
            $table->string('type', 20);
            $table->string('description', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_deposit_transactions');
    }
};
