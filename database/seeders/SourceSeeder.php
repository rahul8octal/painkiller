<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Source::create([
             'name' => 'reddit',
             'type' => 'api',
             'config' => json_encode(['subs' => ['startups', 'entrepreneur']]),
             'active' => true,
         ]);
    }
}
