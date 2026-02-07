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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number', 20)->unique();
            $table->date('entry_date');
            $table->text('description');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->enum('status', ['draft', 'posted', 'void'])->default('posted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
