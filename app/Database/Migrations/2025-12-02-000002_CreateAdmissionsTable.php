<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdmissionsTable extends Migration
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
            'patient_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'doctor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'admission_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'discharge_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'NULL if still admitted',
            ],
            'room_type' => [
                'type' => 'ENUM',
                'constraint' => ['ICU', 'NICU', 'WARD'],
                'null' => false,
            ],
            'room_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Reason for admission',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['admitted', 'discharged'],
                'default' => 'admitted',
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
        $this->forge->addKey('patient_id');
        $this->forge->addKey('doctor_id');
        $this->forge->addKey('status');
        $this->forge->addKey('admission_date');
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('doctor_id', 'doctors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('admissions');
    }

    public function down()
    {
        $this->forge->dropTable('admissions');
    }
}

