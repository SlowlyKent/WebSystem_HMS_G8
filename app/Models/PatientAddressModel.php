<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientAddressModel extends Model
{
    protected $table            = 'patient_addresses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'patient_id',
        'province',
        'city_municipality',
        'barangay',
        'street',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}


