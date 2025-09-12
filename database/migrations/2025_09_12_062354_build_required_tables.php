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
        // Tasks table
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending')->index(indexName: 'idx_tasks_status');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->index(indexName: 'idx_tasks_priority');
            $table->date('due_date')->nullable()->index(indexName: 'idx_tasks_due_date');
            $table->foreignId('assigned_to')->nullable()->constrained(table: 'users', indexName: 'fk_tasks_assigned_to')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('version')->default(1);
            $table->json('metadata')->nullable();
        });

        // Tags table
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // Pivot table for tasks and tags
        Schema::create('tag_task', function (Blueprint $table) {
            $table->foreignId('task_id')->constrained(table: 'tasks', indexName: 'fk_tag_task_id')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained(table: 'tags', indexName: 'fk_tag_task_tag_id')->cascadeOnDelete();
            $table->primary(['task_id', 'tag_id']);
        });

        // Alter users table to add role (for simplicity, will use enum here, but consider a roles table for more complex scenarios)
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin'])->default('user')->after('id');
        });

        // Task log table
        Schema::create('task_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained(table: 'task_log' , indexName:'fk_task_log_task_id')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained(table: 'task_log' , indexName:'fk_task_log_user_id')->cascadeOnDelete();
            $table->json('changes');
            $table->timestamp('created_at')->useCurrent();
            $table->enum('operation_type', ['created', 'updated', 'deleted', 'restored']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_log');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        Schema::dropIfExists('tag_task');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('tasks');
    }
};
