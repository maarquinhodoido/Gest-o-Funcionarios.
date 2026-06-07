<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('reference', 100)->nullable()->after('id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('reference', 100)->nullable()->after('id');
        });
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->string('reference', 100)->nullable()->after('id');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->string('reference', 100)->nullable()->after('id');
        });
        Schema::table('equipment', function (Blueprint $table) {
            $table->string('reference', 100)->nullable()->after('id');
        });
        Schema::table('equipment_assignments', function (Blueprint $table) {
            $table->string('reference', 100)->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
        Schema::table('equipment_assignments', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }
};
