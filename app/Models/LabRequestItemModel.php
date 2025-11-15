<?php

namespace App\Models;

use CodeIgniter\Model;

class LabRequestItemModel extends Model
{
    protected $table            = 'lab_request_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'request_id',
        'test_type_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

