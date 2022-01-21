<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class Users extends Seeder
{
    public function run()
    {
        for ($i = 0; $i < 20; $i++) {
            $this->db->table('users')->insert($this->generateUsers());
        }

        $admin = [
            'email'    => 'admin@code.loc',
            'username' => 'admin',
            'name' => 'Chuck Norris',
            'password' => password_hash('password', PASSWORD_BCRYPT),
            'role_id' => 2
        ];

        $this->db->table('users')->insert($admin);
    }

    private function generateUsers(): array
    {
        $faker = Factory::create();
        $username = $faker->userName();
        return [
            'name' => $faker->name(),
            'email' => $faker->email,
            'username' => $username,
            'password' => password_hash($username, PASSWORD_BCRYPT)
        ];
    }
}
