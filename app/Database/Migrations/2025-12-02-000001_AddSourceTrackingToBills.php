<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSourceTrackingToBills extends Migration
{
    public function up()
    {
        // Add source tracking fields to bills table
        // These fields link bills to their source (appointment, admission, lab, prescription)
        
        if (!$this->db->fieldExists('appointment_id', 'bills')) {
            $this->forge->addColumn('bills', [
                'appointment_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'patient_id',
                    'comment' => 'Links to appointments table if bill is from consultation',
                ],
            ]);
        }

        if (!$this->db->fieldExists('admission_id', 'bills')) {
            $this->forge->addColumn('bills', [
                'admission_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'appointment_id',
                    'comment' => 'Links to admissions table if bill is from room stay',
                ],
            ]);
        }

        if (!$this->db->fieldExists('lab_request_id', 'bills')) {
            $this->forge->addColumn('bills', [
                'lab_request_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'admission_id',
                    'comment' => 'Links to lab_requests table if bill is from lab tests',
                ],
            ]);
        }

        if (!$this->db->fieldExists('prescription_id', 'bills')) {
            $this->forge->addColumn('bills', [
                'prescription_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'lab_request_id',
                    'comment' => 'Links to prescriptions table if bill is from medicine',
                ],
            ]);
        }

        // Add indexes for better performance
        if ($this->db->fieldExists('appointment_id', 'bills')) {
            $this->forge->addKey('appointment_id');
        }
        if ($this->db->fieldExists('admission_id', 'bills')) {
            $this->forge->addKey('admission_id');
        }
        if ($this->db->fieldExists('lab_request_id', 'bills')) {
            $this->forge->addKey('lab_request_id');
        }
        if ($this->db->fieldExists('prescription_id', 'bills')) {
            $this->forge->addKey('prescription_id');
        }
    }

    public function down()
    {
        // Remove the columns we added
        $columns = ['prescription_id', 'lab_request_id', 'admission_id', 'appointment_id'];
        foreach ($columns as $column) {
            if ($this->db->fieldExists($column, 'bills')) {
                $this->forge->dropColumn('bills', $column);
            }
        }
    }
}

