<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Admin Account
            [
                'username' => 'admin',
                'email' => 'admin@hms.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'role_id' => 1, // admin
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Doctor Account
            [
                'username' => 'doctor',
                'email' => 'doctor@hms.com',
                'password' => password_hash('doctor123', PASSWORD_DEFAULT),
                'first_name' => 'John',
                'last_name' => 'Smith',
                'role_id' => 2, // doctor
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Nurse Account
            [
                'username' => 'nurse',
                'email' => 'nurse@hms.com',
                'password' => password_hash('nurse123', PASSWORD_DEFAULT),
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'role_id' => 3, // nurse
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Receptionist Account
            [
                'username' => 'reception',
                'email' => 'reception@hms.com',
                'password' => password_hash('reception123', PASSWORD_DEFAULT),
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'role_id' => 5, // receptionist
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Laboratory Staff Account
            [
                'username' => 'labstaff',
                'email' => 'lab@hms.com',
                'password' => password_hash('labstaff123', PASSWORD_DEFAULT),
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'role_id' => 6, // lab staff
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Pharmacist Account
            [
                'username' => 'pharmacist',
                'email' => 'pharmacy@hms.com',
                'password' => password_hash('pharmacy123', PASSWORD_DEFAULT),
                'first_name' => 'Robert',
                'last_name' => 'Wilson',
                'role_id' => 7, // pharmacist
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Accountant Account
            [
                'username' => 'accountant',
                'email' => 'accounting@hms.com',
                'password' => password_hash(' ', PASSWORD_DEFAULT),
                'first_name' => 'Jennifer',
                'last_name' => 'Lee',
                'role_id' => 8, // accountant
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // IT Staff Account
            [
                'username' => 'itstaff',
                'email' => 'it@hms.com',
                'password' => password_hash('itstaff123', PASSWORD_DEFAULT),
                'first_name' => 'David',
                'last_name' => 'Miller',
                'role_id' => 4, // IT staff
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($data as $user) {
            $existingUser = $this->db->table('users')->where('email', $user['email'])->get()->getRow();
            
            if (!$existingUser) {
                $this->db->table('users')->insert($user);
            }
        }
    }
}
