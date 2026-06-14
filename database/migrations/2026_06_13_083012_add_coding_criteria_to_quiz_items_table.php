<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_items', function (Blueprint $table) {
            $table->text('coding_standards')->nullable()->after('expected_output');
            $table->text('grading_criteria')->nullable()->after('coding_standards');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_items', function (Blueprint $table) {
            $table->dropColumn(['coding_standards', 'grading_criteria']);
        });
    }
};