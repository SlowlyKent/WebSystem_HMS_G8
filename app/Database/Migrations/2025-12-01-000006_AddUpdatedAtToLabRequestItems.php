<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToLabRequestItems extends Migration
{
    public function up()
    {
        // Add updated_at column if it doesn't exist
        if (!$this->db->fieldExists('updated_at', 'lab_request_items')) {
            $this->forge->addColumn('lab_request_items', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
            ]);
        }
    }

    public function down()
    {
        // Remove updated_at column if it exists
        if ($this->db->fieldExists('updated_at', 'lab_request_items')) {
            $this->forge->dropColumn('lab_request_items', 'updated_at');
        }
    }
}

