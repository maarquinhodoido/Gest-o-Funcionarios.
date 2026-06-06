<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_profile_id')->nullable()->after('company_id');
            $table->foreign('employee_profile_id', 'fk_users_employee_profile')
                  ->references('id')->on('employee_profiles')
                  ->onDelete('set null')->onUpdate('no action');
            $table->dropColumn(['nif', 'birth_date', 'niss', 'position']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['employee_profile_id']);
            $table->dropColumn('employee_profile_id');
            $table->string('nif', 20)->nullable()->after('email');
            $table->date('birth_date')->nullable()->after('nif');
            $table->string('niss', 20)->nullable()->after('phone');
            $table->string('position', 100)->nullable()->after('position_id');
        });
    }
};
