<?php

namespace App\Models;

use CodeIgniter\Model;

class LabTestTypeModel extends Model
{
    protected $table            = 'lab_test_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'name',
        'category',
        'price',
        'normal_range',
        'unit',
        'description',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

