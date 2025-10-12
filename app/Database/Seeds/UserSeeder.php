<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin123',
                'email' => 'admin@stpeter.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'role_id' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'itstaff123',
                'email' => 'itstaff@stpeter.com',
                'password' => password_hash('itstaff123', PASSWORD_DEFAULT),
                'first_name' => 'IT',
                'last_name' => 'Staff',
                'role_id' => 4,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'nurse123',
                'email' => 'nurse@stpeter.com',
                'password' => password_hash('nurse123', PASSWORD_DEFAULT),
                'first_name' => 'Jane',
                'last_name' => 'Nurse',
                'role_id' => 3,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($data as $user) {
            $existingUser = $this->db->table('users')->where('email', $user['email'])->get()->getRow();
            
            if (!$existingUser) {
                $this->db->table('users')->insert($user);
            }
        }
    }
}
