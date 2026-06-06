<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->integer('position_id')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('status', 50)->default('active');
            $table->boolean('is_online')->default(false);
            $table->string('profile_photo', 255)->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('last_login_ip', 45)->nullable();
            $table->text('last_login_user_agent')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'company_id', 'department_id', 'position_id', 'hire_date',
                'phone', 'status', 'is_online', 'profile_photo',
                'two_factor_secret', 'two_factor_enabled', 'last_login_ip',
                'last_login_user_agent', 'last_login_at', 'password_changed_at',
            ]);
        });
    }
};
