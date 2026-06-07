<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->string('document_type', 10)->nullable()->after('niss');
            $table->string('document_number', 50)->nullable()->after('document_type');
            $table->date('document_issue_date')->nullable()->after('document_number');
            $table->date('document_expiry_date')->nullable()->after('document_issue_date');
        });
    }

    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn(['document_type', 'document_number', 'document_issue_date', 'document_expiry_date']);
        });
    }
};
