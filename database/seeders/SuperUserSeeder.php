<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@nube.it'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
                'role' => 'superuser',
            ]
        );
    }
}
