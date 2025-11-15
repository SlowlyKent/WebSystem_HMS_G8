<?php

namespace App\Models;

use CodeIgniter\Model;

class PrescriptionItemModel extends Model
{
    protected $table            = 'prescription_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'prescription_id',
        'medicine_id',
        'quantity',
        'unit_price',
        'total_price',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

