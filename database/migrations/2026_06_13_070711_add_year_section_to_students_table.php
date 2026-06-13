<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->tinyInteger('year')->nullable()->after('course'); // 1-4
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete()->after('year');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn(['year', 'section_id']);
        });
    }
};
