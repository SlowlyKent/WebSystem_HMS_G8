<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScheduleTypesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'type_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'unique' => true,
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
        $this->forge->addKey('id', true);
        $this->forge->createTable('schedule_types');

        $data = [
            [
                'type_name' => 'Check-up',
                'description' => 'Regular consultation or general health examination',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'type_name' => 'Follow-up',
                'description' => 'Return visit after previous consultation or treatment',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'type_name' => 'Surgery',
                'description' => 'Scheduled surgical procedure or operation',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'type_name' => 'Emergency',
                'description' => 'Immediate care needed for urgent or severe cases',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'type_name' => 'Vaccination',
                'description' => 'Immunization or booster shot appointment',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'type_name' => 'Nutrition Consultation',
                'description' => 'Dietary advice and meal planning with a nutritionist',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'type_name' => 'Eye Check-up',
                'description' => 'Ophthalmology or optometry appointment for vision and eye health',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('schedule_types')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('schedule_types');
    }
}
