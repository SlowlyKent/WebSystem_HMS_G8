<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'display_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'level' => [
                'type'    => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'is_active' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Primary key
        $this->forge->addKey('id', true);

        $this->forge->createTable('roles');

            // Insert default roles
            $roles = [
                [
                    'name' => 'admin',
                    'display_name' => 'Administrator',
                    'description' => 'Full system access and management',
                    'level' => 100,
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'it_staff',
                    'display_name' => 'IT Staff',
                    'description' => 'IT support and system maintenance',
                    'level' => 80,
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'doctor',
                    'display_name' => 'Doctor',
                    'description' => 'Medical practitioner with patient access',
                    'level' => 60,
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'nurse',
                    'display_name' => 'Nurse',
                    'description' => 'Nursing staff with patient care access',
                    'level' => 40,
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'pharmacist',
                    'display_name' => 'Pharmacist',
                    'description' => 'Pharmacy management and medication access',
                    'level' => 30,
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'receptionist',
                    'display_name' => 'Receptionist',
                    'description' => 'Front desk and appointment management',
                    'level' => 20,
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ];
        $this->db->table('roles')->insertBatch($roles);
    }

    public function down()
    {
        $this->forge->dropTable('roles');
    }
}
