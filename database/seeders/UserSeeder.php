<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert(
            [
                'name' => 'Efat',
                'username' => 'efatkhan',
                'email' => 'efat@example.com',
                'password' => '$2y$12$OZPfGra.RGKUgcJeloq58.ZfCLE14ojkA.DV5hbJ3vKlInTTStTbK' //123456
            ]);
    }
}
