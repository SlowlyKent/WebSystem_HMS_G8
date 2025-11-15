<?php

namespace App\Models;

use CodeIgniter\Model;

class LabRequestModel extends Model
{
    protected $table            = 'lab_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'request_code',
        'patient_id',
        'doctor_id',
        'request_date',
        'status',
        'priority',
        'notes',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate request code like LAB-0001
     */
    public function generateRequestCode()
    {
        $lastRequest = $this->select('request_code')
                           ->orderBy('id', 'DESC')
                           ->first();
        
        $nextNumber = 1;
        
        if ($lastRequest && !empty($lastRequest['request_code']) && 
            preg_match('/^LAB-(\d+)$/', $lastRequest['request_code'], $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }
        
        return 'LAB-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}

