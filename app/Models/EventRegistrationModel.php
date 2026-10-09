<?php

namespace App\Models;

use CodeIgniter\Model;

class EventRegistrationModel extends Model
{
    protected $table            = 'event_registrations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'registration_number',
        'participant_type',
        'user_id',
        'guest_id',
        'status', // pending_verification, pending_approval, confirmed, rejected, cancelled
        'ticket_token_selector',
        'ticket_token_hash',
        'custom_data',
        'notes',
        'registered_at',
        'confirmed_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate unique registration number
     */
    public static function generateRegistrationNumber(): string
    {
        $prefix = 'EVT-' . date('Ymd') . '-';
        $rand   = strtoupper(bin2hex(random_bytes(3))); // 6 hex chars
        return $prefix . $rand;
    }

    /**
     * Generate split ticket token: returns [raw_token, selector, hash]
     */
    public static function generateTicketToken(): array
    {
        $selector = bin2hex(random_bytes(8));   // 16 chars
        $validator = bin2hex(random_bytes(16)); // 32 chars
        $rawToken = $selector . '.' . $validator;
        $hash = hash('sha256', $validator);

        return [
            'raw_token' => $rawToken,
            'selector'  => $selector,
            'hash'      => $hash,
        ];
    }

    /**
     * Get registration by token with event details
     */
    public function getByTicketToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return null;
        }

        [$selector, $validator] = $parts;

        $reg = $this->where('ticket_token_selector', $selector)->first();
        if (! $reg) {
            return null;
        }

        $expectedHash = hash('sha256', $validator);
        if (! hash_equals($reg['ticket_token_hash'], $expectedHash)) {
            return null;
        }

        return $this->getWithDetails($reg['id']);
    }

    /**
     * Get registration with joined participant and event details
     */
    public function getWithDetails(int $id): ?array
    {
        $builder = $this->db->table('event_registrations r')
            ->select('r.*, e.title as event_title, e.slug as event_slug, e.start_date as event_start_date, e.end_date as event_end_date, e.venue_name, e.address as event_address, e.city as event_city, e.allow_kta_checkin, e.status as event_status, e.banner_path')
            ->join('events e', 'e.id = r.event_id')
            ->where('r.id', $id);

        $row = $builder->get()->getRowArray();
        if (! $row) {
            return null;
        }

        // Add participant details
        if ($row['participant_type'] === 'member' && ! empty($row['user_id'])) {
            $memberData = $this->db->table('users u')
                ->select('u.username, ai.secret as user_email, m.member_number as membership_number, m.status as membership_status, mp.full_name, mp.display_name, mp.photo_path as avatar_path, mp.business_name as company_name')
                ->join('auth_identities ai', "ai.user_id = u.id AND ai.type = 'email_password'", 'left')
                ->join('memberships m', 'm.user_id = u.id', 'left')
                ->join('member_profiles mp', 'mp.user_id = u.id', 'left')
                ->where('u.id', $row['user_id'])
                ->get()->getRowArray();

            $row['participant_name'] = $memberData['display_name'] ?? $memberData['full_name'] ?? $memberData['username'] ?? 'Member KOMEO';
            $row['participant_email'] = $memberData['user_email'] ?? '';
            $row['membership_number'] = $memberData['membership_number'] ?? '-';
            $row['membership_status'] = $memberData['membership_status'] ?? '-';
            $row['avatar_path'] = $memberData['avatar_path'] ?? null;
            $row['company'] = $memberData['company_name'] ?? '-';
            $row['job_title'] = $memberData['job_title'] ?? '-';
        } elseif ($row['participant_type'] === 'external' && ! empty($row['guest_id'])) {
            $guestData = $this->db->table('event_guests')->where('id', $row['guest_id'])->get()->getRowArray();
            $row['participant_name'] = $guestData['name'] ?? 'Peserta Umum';
            $row['participant_email'] = $guestData['email'] ?? '';
            $row['participant_whatsapp'] = $guestData['whatsapp'] ?? '-';
            $row['membership_number'] = null;
            $row['membership_status'] = null;
            $row['avatar_path'] = null;
            $row['company'] = $guestData['company'] ?? '-';
            $row['job_title'] = $guestData['job_title'] ?? '-';
        } else {
            $row['participant_name'] = 'Peserta #' . $row['id'];
            $row['participant_email'] = '';
        }

        // Attendance check
        $attendance = $this->db->table('event_attendance')
            ->where('registration_id', $row['id'])
            ->get()->getRowArray();
        $row['attendance'] = $attendance;
        $row['is_attended'] = ! empty($attendance);

        return $row;
    }
}
