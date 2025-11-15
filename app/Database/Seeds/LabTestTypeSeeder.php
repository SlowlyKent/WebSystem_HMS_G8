<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LabTestTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Hematology Tests
            [
                'name' => 'Complete Blood Count (CBC)',
                'category' => 'Hematology',
                'price' => 250.00,
                'normal_range' => 'Varies by component',
                'unit' => 'cells/μL',
                'description' => 'Complete blood count including red cells, white cells, and platelets',
                'status' => 'active',
            ],
            [
                'name' => 'Hemoglobin (Hb)',
                'category' => 'Hematology',
                'price' => 150.00,
                'normal_range' => '12-16 g/dL (Female), 14-18 g/dL (Male)',
                'unit' => 'g/dL',
                'description' => 'Hemoglobin level in blood',
                'status' => 'active',
            ],
            [
                'name' => 'Hematocrit (Hct)',
                'category' => 'Hematology',
                'price' => 150.00,
                'normal_range' => '36-48% (Female), 42-52% (Male)',
                'unit' => '%',
                'description' => 'Percentage of red blood cells in blood',
                'status' => 'active',
            ],

            // Biochemistry Tests
            [
                'name' => 'Blood Glucose (Fasting)',
                'category' => 'Biochemistry',
                'price' => 200.00,
                'normal_range' => '70-100 mg/dL',
                'unit' => 'mg/dL',
                'description' => 'Fasting blood sugar level',
                'status' => 'active',
            ],
            [
                'name' => 'Total Cholesterol',
                'category' => 'Biochemistry',
                'price' => 300.00,
                'normal_range' => '< 200 mg/dL',
                'unit' => 'mg/dL',
                'description' => 'Total cholesterol level',
                'status' => 'active',
            ],
            [
                'name' => 'Liver Function Test (LFT)',
                'category' => 'Biochemistry',
                'price' => 500.00,
                'normal_range' => 'Varies by component',
                'unit' => 'U/L',
                'description' => 'Complete liver function panel',
                'status' => 'active',
            ],
            [
                'name' => 'Creatinine',
                'category' => 'Biochemistry',
                'price' => 200.00,
                'normal_range' => '0.6-1.2 mg/dL',
                'unit' => 'mg/dL',
                'description' => 'Kidney function test',
                'status' => 'active',
            ],

            // Microbiology Tests
            [
                'name' => 'Urine Culture & Sensitivity',
                'category' => 'Microbiology',
                'price' => 400.00,
                'normal_range' => 'No growth',
                'unit' => 'CFU/mL',
                'description' => 'Bacterial culture and antibiotic sensitivity',
                'status' => 'active',
            ],
            [
                'name' => 'Blood Culture',
                'category' => 'Microbiology',
                'price' => 500.00,
                'normal_range' => 'No growth',
                'unit' => 'CFU/mL',
                'description' => 'Bacterial culture from blood sample',
                'status' => 'active',
            ],

            // Serology Tests
            [
                'name' => 'HIV Test',
                'category' => 'Serology',
                'price' => 600.00,
                'normal_range' => 'Non-reactive',
                'unit' => 'N/A',
                'description' => 'HIV antibody test',
                'status' => 'active',
            ],
            [
                'name' => 'Hepatitis B Surface Antigen (HBsAg)',
                'category' => 'Serology',
                'price' => 400.00,
                'normal_range' => 'Negative',
                'unit' => 'N/A',
                'description' => 'Hepatitis B screening',
                'status' => 'active',
            ],
        ];

        // Using Query Builder to insert data
        $this->db->table('lab_test_types')->insertBatch($data);
    }
}

