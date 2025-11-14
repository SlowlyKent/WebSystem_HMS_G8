<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePatientsAddInsuranceBloodType extends Migration
{
    public function up()
    {
        // Add new columns if they don't exist
        if (! $this->db->fieldExists('insurance_provider', 'patients')) {
            $this->forge->addColumn('patients', [
                'insurance_provider' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'status'
                ],
            ]);
        }
        if (! $this->db->fieldExists('blood_type', 'patients')) {
            $this->forge->addColumn('patients', [
                'blood_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                    'null' => true,
                    'after' => 'insurance_provider'
                ],
            ]);
        }

        // Drop old 'room' column if it exists
        if ($this->db->fieldExists('room', 'patients')) {
            $this->forge->dropColumn('patients', 'room');
        }
    }

    public function down()
    {
        // Revert changes
        if ($this->db->fieldExists('insurance_provider', 'patients')) {
            $this->forge->dropColumn('patients', 'insurance_provider');
        }
        if ($this->db->fieldExists('blood_type', 'patients')) {
            $this->forge->dropColumn('patients', 'blood_type');
        }
        // Optionally recreate 'room' column
        if (! $this->db->fieldExists('room', 'patients')) {
            $this->forge->addColumn('patients', [
                'room' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => true,
                    'after' => 'status'
                ],
            ]);
        }
    }
}
