<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(UserTypesSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(AcademicCurriculaSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(DepartmentSectionsSeeder::class);
        $this->call(SubjectsSeeder::class);

    }
}
