<?php

namespace App\Models;

use CodeIgniter\Model;

class BillingModel extends Model
{
    protected $table            = 'bills';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'patient_id',
        'patient_name',
        'bill_date',
        'due_date',
        'description',
        'amount',
        'insured_amount',
        'patient_responsibility',
        'insurance_status',
        'insurance_notes',
        'status',
        'invoice_no',
    ];
}
