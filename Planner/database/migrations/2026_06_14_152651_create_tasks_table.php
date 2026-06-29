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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('task_date');
            $table->integer('estimated_minutes');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('done')->default(false);
            $table->integer('day_before_alarm')->default(0);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->index('user_id');
            $table->foreignId('plan_id')->nullable()->constrained()->restrictOnDelete();
            $table->index('plan_id');
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->index('category_id');
            $table->index('task_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
