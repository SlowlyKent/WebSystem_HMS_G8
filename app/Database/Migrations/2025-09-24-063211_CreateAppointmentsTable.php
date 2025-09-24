<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppointmentsTable extends Migration
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
            'patient_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'doctor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'schedule_type_id' => [ // references schedule_types table
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'appointment_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'appointment_time' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'duration_minutes' => [
                'type'    => 'INT',
                'default' => 30,
                'null'    => false,
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

        // Indexes
        $this->forge->addKey('appointment_date');
        $this->forge->addKey('status');

        // Foreign keys
        $this->forge->addForeignKey('patient_id', 'users', 'id', 'CASCADE', 'CASCADE'); // patients are in users table
        $this->forge->addForeignKey('doctor_id', 'users', 'id', 'CASCADE', 'CASCADE'); 
        $this->forge->addForeignKey('schedule_type_id', 'schedule_types', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('appointments', true, ['ENGINE' => 'InnoDB']);

        // Ensure a doctor cannot have two appointments at the same date/time
        $this->db->query('ALTER TABLE appointments ADD UNIQUE KEY unique_doctor_schedule (doctor_id, appointment_date, appointment_time)');
    }

    public function down()
    {
        $this->forge->dropTable('appointments');
    }
}