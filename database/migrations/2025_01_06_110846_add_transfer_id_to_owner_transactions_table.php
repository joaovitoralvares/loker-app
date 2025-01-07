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
        Schema::table('owner_transactions', function (Blueprint $table) {
            $table->foreignId('transfer_id')->constrained('owner_transfers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owner_transactions', function (Blueprint $table) {
            $table->dropColumn('transfer_id');
        });
    }
};
