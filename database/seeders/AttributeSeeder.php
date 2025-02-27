<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attributes')->insert([
            ['name' => 'department', 'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'start_date', 'type' => 'date', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'end_date', 'type' => 'date', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
