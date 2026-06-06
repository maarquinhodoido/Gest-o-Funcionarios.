<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nif', 20)->nullable()->after('email');
            $table->date('birth_date')->nullable()->after('nif');
            $table->string('niss', 20)->nullable()->after('phone');
            $table->string('position', 100)->nullable()->after('position_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nif', 'birth_date', 'niss', 'position']);
        });
    }
};
