<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModels extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [ 
                    'username',
                    'email',
                    'password',
                    'first_name',
                    'last_name',
                    'role_id',  
                    'status',
                    'created_by',
                    'created_at',
                    'updated_at'
                ];
}