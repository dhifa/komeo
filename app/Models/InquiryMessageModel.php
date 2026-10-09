<?php

namespace App\Models;

use CodeIgniter\Model;

class InquiryMessageModel extends Model
{
    protected $table            = 'inquiry_messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'inquiry_id',
        'sender_type', // client, member
        'sender_id',   // user_id if member, null if client
        'message',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Get all messages in a conversation
     */
    public function getThread(int $inquiryId): array
    {
        return $this->where('inquiry_id', $inquiryId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
