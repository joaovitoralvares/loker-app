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
        Schema::create('contract_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained();
            $table->foreignId('company_id')->constrained();
            $table->bigInteger('amount');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_invoices');
    }
};
