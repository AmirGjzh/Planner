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
            $table->unsignedInteger('estimated_minutes');
            $table->string('priority')->default('medium');
            $table->boolean('done')->default(false);
            $table->unsignedSmallInteger('day_before_alarm')->default(0);
            $table->foreignId('user_id');
            $table->foreignId('plan_id')->nullable();
            $table->foreignId('category_id');
            $table->index(['user_id', 'task_date']);
            $table->index(['user_id', 'done']);
            $table->index('plan_id');
            $table->index('category_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('plan_id')->references('id')->on('plans')->restrictOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
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
