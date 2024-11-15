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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('company_id')->constrained('companies');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widow'])->nullable();
            $table->date('birthday');
            $table->string('rg')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('profession')->nullable();
            $table->string('cnh_number');
            $table->string('cnh_security_code');
            $table->string('cnh_category', 10);
            $table->date('cnh_expiration_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
