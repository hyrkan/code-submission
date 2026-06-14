<?php

namespace Database\Seeders;

use App\Models\Year;
use App\Models\Section;
use Illuminate\Database\Seeder;

class YearSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        Section::query()->delete();
        Year::query()->delete();

        $years = [
            '1st Year',
            '2nd Year',
            '3rd Year',
            '4th Year',
        ];

        $sections = ['A', 'B', 'C', 'D', 'E'];

        foreach ($years as $yearName) {
            $year = Year::create(['name' => $yearName]);

            foreach ($sections as $sectionName) {
                Section::create([
                    'name' => "Section {$sectionName}",
                    'year_id' => $year->id,
                ]);
            }
        }
    }
}