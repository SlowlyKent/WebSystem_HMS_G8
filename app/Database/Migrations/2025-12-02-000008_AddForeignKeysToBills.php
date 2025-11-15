<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddForeignKeysToBills extends Migration
{
    public function up()
    {
        // Add foreign key constraints for source tracking fields
        // Note: We check if tables exist first to avoid errors
        
        // Foreign key for appointment_id
        if ($this->db->fieldExists('appointment_id', 'bills') && $this->db->tableExists('appointments')) {
            try {
                $this->db->query("ALTER TABLE bills 
                    ADD CONSTRAINT fk_bills_appointment 
                    FOREIGN KEY (appointment_id) REFERENCES appointments(id) 
                    ON DELETE SET NULL ON UPDATE CASCADE");
            } catch (\Exception $e) {
                // Constraint might already exist, ignore
            }
        }

        // Foreign key for admission_id
        if ($this->db->fieldExists('admission_id', 'bills') && $this->db->tableExists('admissions')) {
            try {
                $this->db->query("ALTER TABLE bills 
                    ADD CONSTRAINT fk_bills_admission 
                    FOREIGN KEY (admission_id) REFERENCES admissions(id) 
                    ON DELETE SET NULL ON UPDATE CASCADE");
            } catch (\Exception $e) {
                // Constraint might already exist, ignore
            }
        }

        // Foreign key for lab_request_id
        if ($this->db->fieldExists('lab_request_id', 'bills') && $this->db->tableExists('lab_requests')) {
            try {
                $this->db->query("ALTER TABLE bills 
                    ADD CONSTRAINT fk_bills_lab_request 
                    FOREIGN KEY (lab_request_id) REFERENCES lab_requests(id) 
                    ON DELETE SET NULL ON UPDATE CASCADE");
            } catch (\Exception $e) {
                // Constraint might already exist, ignore
            }
        }

        // Foreign key for prescription_id
        if ($this->db->fieldExists('prescription_id', 'bills') && $this->db->tableExists('prescriptions')) {
            try {
                $this->db->query("ALTER TABLE bills 
                    ADD CONSTRAINT fk_bills_prescription 
                    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) 
                    ON DELETE SET NULL ON UPDATE CASCADE");
            } catch (\Exception $e) {
                // Constraint might already exist, ignore
            }
        }
    }

    public function down()
    {
        // Drop foreign keys (if they exist)
        $constraints = [
            'fk_bills_appointment',
            'fk_bills_admission',
            'fk_bills_lab_request',
            'fk_bills_prescription',
        ];

        foreach ($constraints as $constraint) {
            try {
                $this->db->query("ALTER TABLE bills DROP FOREIGN KEY {$constraint}");
            } catch (\Exception $e) {
                // Constraint might not exist, ignore
            }
        }
    }
}

