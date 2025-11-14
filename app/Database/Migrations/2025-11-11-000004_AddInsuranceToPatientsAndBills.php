<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInsuranceToPatientsAndBills extends Migration
{
    public function up()
    {
        // Patients: add policy_no, coverage_pct, max_per_bill, valid_until
        if (! $this->db->fieldExists('insurance_policy_no', 'patients')) {
            $this->forge->addColumn('patients', [
                'insurance_policy_no' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'insurance_provider'
                ],
            ]);
        }
        if (! $this->db->fieldExists('insurance_coverage_pct', 'patients')) {
            $this->forge->addColumn('patients', [
                'insurance_coverage_pct' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'null' => true,
                    'after' => 'insurance_policy_no'
                ],
            ]);
        }
        if (! $this->db->fieldExists('insurance_max_per_bill', 'patients')) {
            $this->forge->addColumn('patients', [
                'insurance_max_per_bill' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'null' => true,
                    'after' => 'insurance_coverage_pct'
                ],
            ]);
        }
        if (! $this->db->fieldExists('insurance_valid_until', 'patients')) {
            $this->forge->addColumn('patients', [
                'insurance_valid_until' => [
                    'type' => 'DATE',
                    'null' => true,
                    'after' => 'insurance_max_per_bill'
                ],
            ]);
        }

        // Bills: add patient_id, insured_amount, patient_responsibility, insurance_status, insurance_notes
        if (! $this->db->fieldExists('patient_id', 'bills')) {
            $this->forge->addColumn('bills', [
                'patient_id' => [
                    'type' => 'INT',
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'id'
                ],
            ]);
        }
        if (! $this->db->fieldExists('insured_amount', 'bills')) {
            $this->forge->addColumn('bills', [
                'insured_amount' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'null' => false,
                    'default' => '0.00',
                    'after' => 'amount'
                ],
            ]);
        }
        if (! $this->db->fieldExists('patient_responsibility', 'bills')) {
            $this->forge->addColumn('bills', [
                'patient_responsibility' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'null' => false,
                    'default' => '0.00',
                    'after' => 'insured_amount'
                ],
            ]);
        }
        if (! $this->db->fieldExists('insurance_status', 'bills')) {
            $this->forge->addColumn('bills', [
                'insurance_status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => false,
                    'default' => 'none',
                    'after' => 'patient_responsibility'
                ],
            ]);
        }
        if (! $this->db->fieldExists('insurance_notes', 'bills')) {
            $this->forge->addColumn('bills', [
                'insurance_notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'insurance_status'
                ],
            ]);
        }

        // Optional: add index on patient_id
        $this->forge->addKey('patient_id');
    }

    public function down()
    {
        // Drop bills columns
        foreach (['insurance_notes','insurance_status','patient_responsibility','insured_amount','patient_id'] as $col) {
            if ($this->db->fieldExists($col, 'bills')) {
                $this->forge->dropColumn('bills', $col);
            }
        }
        // Drop patients columns
        foreach (['insurance_valid_until','insurance_max_per_bill','insurance_coverage_pct','insurance_policy_no'] as $col) {
            if ($this->db->fieldExists($col, 'patients')) {
                $this->forge->dropColumn('patients', $col);
            }
        }
    }
}
