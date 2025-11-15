<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table            = 'patients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'patient_code',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'status',
        'insurance_provider',
        'insurance_policy_no',
        'insurance_coverage_pct',
        'insurance_max_per_bill',
        'insurance_valid_until',
        'blood_type',
        'medical_notes',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate the next patient code in the format P-0001
     * 
     * @return string
     */
    public function generatePatientCode()
    {
        // Find the highest existing code
        $lastPatient = $this->select('patient_code')
                           ->orderBy('id', 'DESC')
                           ->first();
        
        $nextNumber = 1;
        
        if ($lastPatient && !empty($lastPatient['patient_code']) && 
            preg_match('/^P-(\d+)$/', $lastPatient['patient_code'], $matches)) {
            // If we have a matching code, increment the number
            $nextNumber = (int)$matches[1] + 1;
        }
        
        // Format with leading zeros and return
        return 'P-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Before insert callback to auto-generate patient code if not provided
     */
    protected function beforeInsert(array $data)
    {
        if (empty($data['data']['patient_code'])) {
            $data['data']['patient_code'] = $this->generatePatientCode();
        }
        
        return $data;
    }
}
