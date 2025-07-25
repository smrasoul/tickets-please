<?php

namespace Database\Seeders;

use App\Models\ticket;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\TicketsFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $users = User::factory(10)->create();

         Ticket::factory(100)
             ->recycle($users)
             ->create();

         User::create([
             'email' => 'manager@manager.com',
             'password' => bcrypt('password'),
             'name' => 'The Manager',
             'is_manager' => true,
         ]);

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);
    }
}
