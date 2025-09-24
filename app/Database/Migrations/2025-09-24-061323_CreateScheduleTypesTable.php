<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScheduleTypesTable extends Migration
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
            'description' => [
                'type' => 'TEXT',
                'null' => true,
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

        // Unique name
        $this->forge->addUniqueKey('name');

        // Create table
        $this->forge->createTable('schedule_types');

        // Insert default schedule types
        $defaultTypes = [
            ['name' => 'Consultation', 'description' => 'General consultation session', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Check-up', 'description' => 'Routine health check', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Surgery', 'description' => 'Surgical procedure', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Rounds', 'description' => 'Doctor rounds in the hospital', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Emergency', 'description' => 'Emergency cases', 'created_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('schedule_types')->insertBatch($defaultTypes);
    }

    public function down()
    {
        $this->forge->dropTable('schedule_types');
    }
}