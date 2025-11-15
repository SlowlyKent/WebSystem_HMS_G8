<?php

namespace App\Models;

use CodeIgniter\Model;

class LabSampleModel extends Model
{
    protected $table            = 'lab_samples';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'request_id',
        'sample_type',
        'collection_date',
        'collected_by',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

