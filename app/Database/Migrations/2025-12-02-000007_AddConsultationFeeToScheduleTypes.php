<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddConsultationFeeToScheduleTypes extends Migration
{
    public function up()
    {
        // Add consultation_fee field to schedule_types table
        if (!$this->db->fieldExists('consultation_fee', 'schedule_types')) {
            $this->forge->addColumn('schedule_types', [
                'consultation_fee' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => false,
                    'default' => '500.00',
                    'after' => 'description',
                    'comment' => 'Consultation fee for this appointment type',
                ],
            ]);

            // Update existing records with default fees
            $defaultFees = [
                'Check-up' => 500.00,
                'Follow-up' => 400.00,
                'Surgery' => 5000.00,
                'Emergency' => 1000.00,
                'Vaccination' => 300.00,
                'Nutrition Consultation' => 600.00,
                'Eye Check-up' => 700.00,
            ];

            foreach ($defaultFees as $typeName => $fee) {
                $this->db->table('schedule_types')
                    ->where('type_name', $typeName)
                    ->update(['consultation_fee' => $fee]);
            }
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('consultation_fee', 'schedule_types')) {
            $this->forge->dropColumn('schedule_types', 'consultation_fee');
        }
    }
}

