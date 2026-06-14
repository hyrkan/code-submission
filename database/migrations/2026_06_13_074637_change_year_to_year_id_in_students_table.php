<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('year');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('year_id')->nullable()->constrained('years')->nullOnDelete()->after('course');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropColumn('year_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->tinyInteger('year')->nullable()->after('course');
        });
    }
};