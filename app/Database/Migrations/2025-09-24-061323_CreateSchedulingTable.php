<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchedulesTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'schedule_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'schedule_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'start_time' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'end_time' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'location' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['scheduled', 'in_progress', 'completed', 'cancelled', 'rescheduled'],
                'default'    => 'scheduled',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        // Primary key
        $this->forge->addKey('id', true);

        // Create table with InnoDB
        $this->forge->createTable('schedules', true, ['ENGINE' => 'InnoDB']);

        // Indexes
        $this->db->query('ALTER TABLE schedules ADD INDEX idx_user_date (user_id, schedule_date)');
        $this->db->query('ALTER TABLE schedules ADD INDEX idx_schedule_date (schedule_date)');
        $this->db->query('ALTER TABLE schedules ADD INDEX idx_status (status)');

        // Foreign keys
        $this->db->query('ALTER TABLE schedules ADD CONSTRAINT fk_schedules_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        $this->db->query('ALTER TABLE schedules ADD CONSTRAINT fk_schedules_type FOREIGN KEY (schedule_type_id) REFERENCES schedule_types(id) ON DELETE RESTRICT');
        $this->db->query('ALTER TABLE schedules ADD CONSTRAINT fk_schedules_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE');
    }

    public function down()
    {
        $this->forge->dropTable('schedules');
    }
}