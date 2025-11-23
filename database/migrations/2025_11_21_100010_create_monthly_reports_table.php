<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            // store first day of month (e.g. 2025-11-01)
            $table->date('month')->index();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->string('export_path')->nullable();
            $table->json('snapshot')->nullable(); // optional: audit snapshot at generate-time
            $table->timestamps();

            $table->unique(['month']); // one report per month
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};