<?php

namespace App\Models;

use CodeIgniter\Model;

class AdmissionModel extends Model
{
    protected $table            = 'admissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'patient_id',
        'doctor_id',
        'admission_date',
        'discharge_date',
        'room_type',
        'room_number',
        'reason',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

