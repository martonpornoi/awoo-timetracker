<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->restrictOnDelete();

            $table->date('date')->index();
            $table->unsignedInteger('minutes'); // multiples of 15 only
            $table->text('description')->nullable();

            $table->foreignId('locked_by_report_id')
                ->nullable()
                ->constrained('monthly_reports')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['project_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
