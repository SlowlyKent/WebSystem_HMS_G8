<?php

namespace App\Models;

use CodeIgniter\Model;

class RoomRateModel extends Model
{
    protected $table            = 'room_rates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'room_type',
        'daily_rate',
        'effective_date',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get current active rate for a room type
     */
    public function getCurrentRate($roomType)
    {
        return $this->where('room_type', $roomType)
                   ->where('is_active', 1)
                   ->where('effective_date <=', date('Y-m-d'))
                   ->orderBy('effective_date', 'DESC')
                   ->first();
    }
}

