<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Artisan;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CoursesTableSeeder::class,
        ]);
        if (app()->environment(['local', 'staging'])) {
            try {
                Artisan::call('schedule:generate');
                $this->command->info('Schedule generated successfully');
            } catch (\Throwable $e) {
                $this->command->error('Schedule generation failed: '.$e->getMessage());
                logger()->error('Schedule generation error', ['exception' => $e]);
            }
        } else {
            $this->command->warn('Schedule generation skipped in production environment');
        }
    }
}
